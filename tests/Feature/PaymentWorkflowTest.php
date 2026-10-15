<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function financeRoles(): array
    {
        return [
            'system administrator' => ['System Administrator'],
            'finance officer' => ['Finance Officer'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function otherRoles(): array
    {
        return [
            'estates manager' => ['University Property / Estates Manager'],
            'university management' => ['University Management'],
            'campus property officer' => ['Campus Property Officer'],
        ];
    }

    #[DataProvider('financeRoles')]
    public function test_finance_roles_can_record_payments(string $role): void
    {
        $user = $this->userWithRole($role);
        $invoice = Invoice::factory()->create(['subtotal' => 100000, 'total_amount' => 100000]);

        $this->actingAs($user)->get(route('payments.index'))->assertSee('Record payment');
        $this->actingAs($user)->get(route('payments.create'))->assertOk();

        $this->actingAs($user)->post(route('payments.store'), $this->paymentPayload($invoice))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('payments', ['invoice_id' => $invoice->id, 'amount' => 40000, 'created_by' => $user->id]);
    }

    #[DataProvider('otherRoles')]
    public function test_other_roles_cannot_record_or_reverse_payments(string $role): void
    {
        $user = $this->userWithRole($role);
        $invoice = Invoice::factory()->create(['subtotal' => 100000, 'total_amount' => 100000]);
        $payment = Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 40000]);

        $this->actingAs($user)->get(route('payments.index'))->assertOk()->assertDontSee('Record payment');
        $this->actingAs($user)->get(route('payments.show', $payment))->assertOk()->assertDontSee('Controlled reversal');
        $this->actingAs($user)->get(route('payments.create'))->assertForbidden();
        $this->actingAs($user)->post(route('payments.store'), $this->paymentPayload($invoice))->assertForbidden();
        $this->actingAs($user)->patch(route('payments.reverse', $payment), ['reason' => 'Trying to undo a payment without rights.'])->assertForbidden();

        $this->assertDatabaseCount('payments', 1);
        $this->assertSame('Recorded', $payment->refresh()->status);
    }

    public function test_user_who_recorded_a_payment_cannot_reverse_it(): void
    {
        $officer = $this->userWithRole('Finance Officer');
        $payment = Payment::factory()->create(['created_by' => $officer->id]);

        $this->actingAs($officer)->get(route('payments.show', $payment))
            ->assertSee('Reversal needs a second person')
            ->assertDontSee('Controlled reversal');

        $this->actingAs($officer)->patch(route('payments.reverse', $payment), ['reason' => 'Undoing my own entry quietly.'])->assertForbidden();

        $this->assertSame('Recorded', $payment->refresh()->status);
    }

    #[DataProvider('financeRoles')]
    public function test_a_different_finance_user_can_reverse_a_payment(string $role): void
    {
        $recorder = $this->userWithRole('Finance Officer');
        $reviewer = $this->userWithRole($role);
        $invoice = Invoice::factory()->create(['subtotal' => 100000, 'total_amount' => 100000]);
        $payment = Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 40000, 'created_by' => $recorder->id]);

        $this->actingAs($reviewer)->get(route('payments.show', $payment))->assertSee('Controlled reversal');

        $this->actingAs($reviewer)->patch(route('payments.reverse', $payment), ['reason' => 'Cash entry was posted to the wrong invoice.'])
            ->assertSessionHasNoErrors();

        $payment->refresh();
        $this->assertSame('Reversed', $payment->status);
        $this->assertSame($reviewer->id, $payment->reversed_by);
        $this->assertSame(100000.0, $invoice->refresh()->balance());
    }

    public function test_billing_service_blocks_self_reversal(): void
    {
        $officer = $this->userWithRole('Finance Officer');
        $payment = Payment::factory()->create(['created_by' => $officer->id]);

        $this->expectException(ValidationException::class);

        app(BillingService::class)->reversePayment($payment, 'Undoing my own entry quietly.', $officer);
    }

    public function test_reversed_payment_cannot_be_reversed_again(): void
    {
        $payment = Payment::factory()->create(['status' => 'Reversed']);

        $this->actingAs($this->userWithRole('System Administrator'))
            ->patch(route('payments.reverse', $payment), ['reason' => 'Second reversal attempt on same payment.'])
            ->assertForbidden();
    }

    public function test_record_payment_form_excludes_draft_invoices(): void
    {
        $draft = Invoice::factory()->create(['status' => 'Draft', 'reference' => 'INV-DRAFT-001']);
        $issued = Invoice::factory()->create(['reference' => 'INV-ISSUED-001']);

        $this->actingAs($this->userWithRole('Finance Officer'))->get(route('payments.create'))
            ->assertOk()
            ->assertSee($issued->reference)
            ->assertDontSee($draft->reference);
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role_id' => Role::query()->firstOrCreate(['name' => $role])->id]);
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentPayload(Invoice $invoice): array
    {
        return [
            'invoice_id' => $invoice->id,
            'paid_at' => now()->subMinute()->format('Y-m-d H:i:s'),
            'amount' => 40000,
            'payment_method' => 'Mobile Money',
            'payment_reference' => 'MM-'.fake()->unique()->numerify('########'),
        ];
    }
}
