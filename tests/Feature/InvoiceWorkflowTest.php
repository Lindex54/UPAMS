<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InvoiceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function managingRoles(): array
    {
        return [
            'system administrator' => ['System Administrator'],
            'finance officer' => ['Finance Officer'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function nonManagingRoles(): array
    {
        return [
            'estates manager' => ['University Property / Estates Manager'],
            'university management' => ['University Management'],
            'campus property officer' => ['Campus Property Officer'],
        ];
    }

    #[DataProvider('managingRoles')]
    public function test_managing_roles_can_generate_invoices(string $role): void
    {
        $user = $this->userWithRole($role);

        $this->actingAs($user)->get(route('billing.create'))->assertOk();
        $this->actingAs($user)->get(route('billing.index'))->assertSee('Create invoice');

        $this->actingAs($user)->post(route('billing.store'), $this->invoicePayload())
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('invoices', ['description' => 'Monthly rent for Shop 4', 'created_by' => $user->id]);
    }

    #[DataProvider('nonManagingRoles')]
    public function test_other_roles_cannot_generate_or_manage_invoices(string $role): void
    {
        $user = $this->userWithRole($role);
        $invoice = Invoice::factory()->create();

        $this->actingAs($user)->get(route('billing.index'))->assertOk()->assertDontSee('Create invoice');
        $this->actingAs($user)->get(route('billing.show', $invoice))->assertOk()->assertDontSee('Update details')->assertDontSee('Cancel invoice');
        $this->actingAs($user)->get(route('billing.create'))->assertForbidden();
        $this->actingAs($user)->post(route('billing.store'), $this->invoicePayload())->assertForbidden();
        $this->actingAs($user)->get(route('billing.edit', $invoice))->assertForbidden();
        $this->actingAs($user)->put(route('billing.update', $invoice), ['description' => 'Changed'])->assertForbidden();
        $this->actingAs($user)->patch(route('billing.archive', $invoice), ['status' => 'Cancelled', 'reason' => 'Raised against the wrong tenant.'])->assertForbidden();

        $this->assertDatabaseCount('invoices', 1);
        $this->assertSame('Issued', $invoice->refresh()->status);
    }

    public function test_user_without_a_role_cannot_generate_invoices(): void
    {
        $this->actingAs(User::factory()->create())->post(route('billing.store'), $this->invoicePayload())->assertForbidden();
    }

    public function test_draft_invoice_remains_fully_editable(): void
    {
        $officer = $this->userWithRole('Finance Officer');
        $invoice = Invoice::factory()->create(['status' => 'Draft']);

        $this->actingAs($officer)->put(route('billing.update', $invoice), $this->invoicePayload(['subtotal' => 250000, 'tax_amount' => 45000, 'status' => 'Draft']))
            ->assertSessionHasNoErrors();

        $this->assertSame('295000.00', $invoice->refresh()->total_amount);
        $this->assertSame('Draft', $invoice->status);
    }

    public function test_issued_invoice_rejects_changes_to_financial_terms(): void
    {
        $officer = $this->userWithRole('Finance Officer');
        $invoice = Invoice::factory()->create(['subtotal' => 100000, 'total_amount' => 100000]);

        $this->actingAs($officer)->put(route('billing.update', $invoice), $this->invoicePayload(['subtotal' => 1000]))
            ->assertSessionHasErrors(['subtotal', 'beneficiary_id', 'campus_id', 'issue_date', 'due_date', 'status']);

        $this->assertSame('100000.00', $invoice->refresh()->total_amount);
    }

    public function test_issued_invoice_allows_descriptive_corrections_only(): void
    {
        $officer = $this->userWithRole('Finance Officer');
        $invoice = Invoice::factory()->create(['subtotal' => 100000, 'total_amount' => 100000]);

        $this->actingAs($officer)->get(route('billing.edit', $invoice))->assertOk()->assertSee('financial terms are locked');

        $this->actingAs($officer)->put(route('billing.update', $invoice), [
            'description' => 'Rent for Shop 4, Main Campus (corrected wording)',
            'property_reference' => 'COM-0214',
        ])->assertSessionHasNoErrors();

        $invoice->refresh();
        $this->assertSame('Rent for Shop 4, Main Campus (corrected wording)', $invoice->description);
        $this->assertSame('COM-0214', $invoice->property_reference);
        $this->assertSame('100000.00', $invoice->total_amount);
    }

    public function test_billing_service_refuses_financial_changes_to_an_issued_invoice(): void
    {
        $invoice = Invoice::factory()->create();

        $this->expectException(ValidationException::class);

        app(BillingService::class)->saveInvoice(['description' => 'Changed', 'subtotal' => 1], $this->userWithRole('System Administrator'), $invoice);
    }

    public function test_unpaid_invoice_can_be_cancelled(): void
    {
        $officer = $this->userWithRole('Finance Officer');
        $invoice = Invoice::factory()->create();

        $this->actingAs($officer)->patch(route('billing.archive', $invoice), ['status' => 'Cancelled', 'reason' => 'Raised against the wrong tenant.'])
            ->assertSessionHasNoErrors();

        $this->assertSame('Cancelled', $invoice->refresh()->status);
        $this->actingAs($officer)->get(route('billing.edit', $invoice))->assertForbidden();
    }

    public function test_invoice_with_payments_cannot_be_cancelled(): void
    {
        $administrator = $this->userWithRole('System Administrator');
        $invoice = Invoice::factory()->create();
        Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 40000]);

        $this->actingAs($administrator)->get(route('billing.show', $invoice))->assertSee('Cancellation unavailable');

        $this->actingAs($administrator)->patch(route('billing.archive', $invoice), ['status' => 'Cancelled', 'reason' => 'Trying to void a paid invoice.'])
            ->assertSessionHasErrors('status');

        $this->assertNotSame('Cancelled', $invoice->refresh()->status);
    }

    public function test_invoice_can_be_cancelled_after_its_payments_are_reversed(): void
    {
        $administrator = $this->userWithRole('System Administrator');
        $invoice = Invoice::factory()->create();
        $payment = Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 40000]);

        app(BillingService::class)->reversePayment($payment, 'Payment was posted to the wrong invoice.', $administrator);

        $this->actingAs($administrator)->patch(route('billing.archive', $invoice), ['status' => 'Cancelled', 'reason' => 'Invoice was raised in error.'])
            ->assertSessionHasNoErrors();

        $this->assertSame('Cancelled', $invoice->refresh()->status);
    }

    public function test_only_fully_paid_invoices_can_be_archived(): void
    {
        $officer = $this->userWithRole('Finance Officer');
        $partiallyPaid = Invoice::factory()->create(['subtotal' => 100000, 'total_amount' => 100000]);
        Payment::factory()->create(['invoice_id' => $partiallyPaid->id, 'amount' => 40000]);
        $fullyPaid = Invoice::factory()->create(['subtotal' => 100000, 'total_amount' => 100000]);
        Payment::factory()->create(['invoice_id' => $fullyPaid->id, 'amount' => 100000]);

        $this->actingAs($officer)->patch(route('billing.archive', $partiallyPaid), ['status' => 'Archived', 'reason' => 'Trying to hide the open balance.'])
            ->assertSessionHasErrors('status');
        $this->actingAs($officer)->patch(route('billing.archive', $fullyPaid), ['status' => 'Archived', 'reason' => 'Settled in full, filing away.'])
            ->assertSessionHasNoErrors();

        $this->assertNotSame('Archived', $partiallyPaid->refresh()->status);
        $this->assertSame('Archived', $fullyPaid->refresh()->status);
    }

    public function test_payments_cannot_be_recorded_against_a_draft_invoice(): void
    {
        $invoice = Invoice::factory()->create(['status' => 'Draft']);

        $this->expectException(ValidationException::class);

        app(BillingService::class)->recordPayment(['invoice_id' => $invoice->id, 'paid_at' => now(), 'amount' => 1000, 'payment_method' => 'Cash'], $this->userWithRole('Finance Officer'));
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role_id' => Role::factory()->create(['name' => $role])->id]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function invoicePayload(array $overrides = []): array
    {
        return $overrides + [
            'beneficiary_id' => Beneficiary::factory()->create()->id,
            'campus_id' => Campus::factory()->create()->id,
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addDays(30)->toDateString(),
            'description' => 'Monthly rent for Shop 4',
            'subtotal' => 500000,
            'tax_amount' => 0,
            'status' => 'Issued',
        ];
    }
}
