<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\ArrearsCalculationService;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArrearsTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_invoice_appears_with_automatic_days_aging_and_full_balance(): void
    {
        $this->travelTo('2026-09-10 10:00:00');
        $administrator = User::factory()->create();
        Invoice::factory()->create([
            'reference' => 'INV-OVERDUE',
            'property_reference' => 'PROP-100',
            'agreement_reference' => 'AGR-100',
            'due_date' => '2026-09-01',
            'subtotal' => 500000,
            'total_amount' => 500000,
        ]);

        $response = $this->actingAs($administrator)->get(route('arrears.index'));

        $response
            ->assertOk()
            ->assertSeeText('Invoice Number')
            ->assertSeeText('Original Due Date')
            ->assertSeeText('Days Overdue')
            ->assertSeeText('Last Payment Date')
            ->assertSeeText('INV-OVERDUE')
            ->assertSeeText('PROP-100')
            ->assertSeeText('AGR-100')
            ->assertSeeText('09')
            ->assertSeeText('Under 30 Days')
            ->assertSeeText('UGX 500,000.00');
    }

    public function test_partially_paid_overdue_invoice_shows_only_remaining_balance_and_last_valid_payment_date(): void
    {
        $this->travelTo('2026-09-10 10:00:00');
        $administrator = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'reference' => 'INV-PARTIAL',
            'due_date' => '2026-09-01',
            'subtotal' => 500000,
            'total_amount' => 500000,
        ]);
        app(BillingService::class)->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 300000,
            'paid_at' => '2026-09-05 14:00:00',
            'payment_method' => 'Bank Transfer',
        ], $administrator);

        $response = $this->actingAs($administrator)->get(route('arrears.index'));

        $response
            ->assertOk()
            ->assertSeeText('INV-PARTIAL')
            ->assertSeeText('UGX 200,000.00')
            ->assertSeeText('05 Sep 2026')
            ->assertDontSeeText('UGX 500,000.00');
    }

    public function test_fully_paid_overdue_invoice_never_appears_in_arrears_even_with_stale_invoice_status(): void
    {
        $this->travelTo('2026-09-10 10:00:00');
        $administrator = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'reference' => 'INV-PAID',
            'due_date' => '2026-09-01',
            'subtotal' => 500000,
            'total_amount' => 500000,
            'status' => 'Issued',
        ]);
        Payment::factory()->for($invoice)->create(['amount' => 500000, 'status' => Payment::STATUS_RECORDED]);

        $response = $this->actingAs($administrator)->get(route('arrears.index'));

        $response->assertOk()->assertDontSeeText('INV-PAID');
        $this->actingAs($administrator)->get(route('arrears.statement', $invoice))->assertNotFound();
    }

    public function test_future_due_invoice_does_not_appear_in_arrears(): void
    {
        $this->travelTo('2026-09-10 10:00:00');
        $administrator = User::factory()->create();
        Invoice::factory()->create(['reference' => 'INV-FUTURE', 'due_date' => '2026-09-11']);

        $response = $this->actingAs($administrator)->get(route('arrears.index'));

        $response->assertOk()->assertDontSeeText('INV-FUTURE');
    }

    public function test_reversing_a_full_payment_returns_the_invoice_to_arrears_automatically(): void
    {
        $this->travelTo('2026-09-10 10:00:00');
        $administrator = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'reference' => 'INV-REVERSED',
            'due_date' => '2026-08-01',
            'subtotal' => 500000,
            'total_amount' => 500000,
        ]);
        $billing = app(BillingService::class);
        $payment = $billing->recordPayment([
            'invoice_id' => $invoice->id,
            'paid_at' => now(),
            'amount' => 500000,
            'payment_method' => 'Cash',
        ], $administrator);

        $this->assertFalse(app(ArrearsCalculationService::class)->query()->whereKey($invoice)->exists());

        $billing->reversePayment($payment, 'The payment was applied to the wrong invoice.', $administrator);

        $arrearsInvoice = app(ArrearsCalculationService::class)->query()->whereKey($invoice)->sole();
        $this->assertSame(500000.0, $arrearsInvoice->balance());
        $this->assertNull($arrearsInvoice->last_payment_at);
    }

    public function test_aging_summary_uses_the_required_boundary_categories(): void
    {
        $this->travelTo('2026-09-10 10:00:00');

        foreach ([29 => 100, 30 => 200, 60 => 300, 61 => 400, 90 => 500, 91 => 600] as $days => $amount) {
            Invoice::factory()->create([
                'due_date' => today()->subDays($days),
                'subtotal' => $amount,
                'total_amount' => $amount,
            ]);
        }

        $summary = app(ArrearsCalculationService::class)->summary();

        $this->assertSame(100.0, $summary['Under 30 Days']);
        $this->assertSame(500.0, $summary['30–60 Days']);
        $this->assertSame(900.0, $summary['60–90 Days']);
        $this->assertSame(600.0, $summary['Over 90 Days']);
    }

    public function test_arrears_filters_match_invoice_beneficiary_campus_and_property_or_agreement(): void
    {
        $this->travelTo('2026-09-10 10:00:00');
        $campus = Campus::factory()->create(['name' => 'Matching Campus']);
        $beneficiary = Beneficiary::factory()->create([
            'campus_id' => $campus->id,
            'full_name_organization' => 'Matching Beneficiary',
        ]);
        $invoice = Invoice::factory()->create([
            'reference' => 'INV-MATCH-001',
            'beneficiary_id' => $beneficiary->id,
            'campus_id' => $campus->id,
            'property_reference' => 'PROPERTY-MATCH',
            'agreement_reference' => 'AGREEMENT-MATCH',
            'due_date' => '2026-09-01',
        ]);
        Invoice::factory()->create(['reference' => 'INV-OTHER-001', 'due_date' => '2026-09-01']);
        $arrears = app(ArrearsCalculationService::class);

        $this->assertSame([$invoice->id], $arrears->query(['q' => 'MATCH-001'])->pluck('id')->all());
        $this->assertSame([$invoice->id], $arrears->query(['q' => 'Matching Beneficiary'])->pluck('id')->all());
        $this->assertSame([$invoice->id], $arrears->query(['campus_id' => $campus->id])->pluck('id')->all());
        $this->assertSame([$invoice->id], $arrears->query(['beneficiary_id' => $beneficiary->id])->pluck('id')->all());
        $this->assertSame([$invoice->id], $arrears->query(['property_reference' => 'PROPERTY-MATCH'])->pluck('id')->all());
        $this->assertSame([$invoice->id], $arrears->query(['property_reference' => 'AGREEMENT-MATCH'])->pluck('id')->all());
    }

    public function test_invalid_arrears_filters_are_rejected(): void
    {
        $administrator = User::factory()->create();

        $response = $this->actingAs($administrator)->get(route('arrears.index', [
            'campus_id' => 999999,
            'beneficiary_id' => 999999,
        ]));

        $response->assertSessionHasErrors(['campus_id', 'beneficiary_id']);
    }
}
