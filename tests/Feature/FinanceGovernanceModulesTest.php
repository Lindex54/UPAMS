<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\User;
use App\Services\ApprovalWorkflowService;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use LogicException;
use Tests\TestCase;

class FinanceGovernanceModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_administrator_can_open_all_finance_and_governance_modules(): void
    {
        $administrator = User::factory()->create();

        foreach (['billing.index', 'payments.index', 'arrears.index', 'utilities.index', 'approvals.index', 'notifications.index', 'reports.index', 'audit-trail.index'] as $route) {
            $this->actingAs($administrator)->get(route($route))->assertOk();
        }
    }

    public function test_payment_updates_invoice_balance_and_can_be_reversed(): void
    {
        $administrator = User::factory()->create();
        $invoice = Invoice::factory()->create(['total_amount' => 100000, 'subtotal' => 100000]);
        $service = app(BillingService::class);

        $payment = $service->recordPayment(['invoice_id' => $invoice->id, 'paid_at' => now(), 'amount' => 40000, 'payment_method' => 'Cash'], $administrator);

        $this->assertSame(60000.0, $invoice->refresh()->balance());
        $service->reversePayment($payment, 'Cash entry was allocated to the wrong invoice.', $administrator);
        $this->assertSame(100000.0, $invoice->refresh()->balance());
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'Reversed']);
    }

    public function test_overpayment_is_rejected_inside_the_billing_transaction(): void
    {
        $this->expectException(ValidationException::class);

        app(BillingService::class)->recordPayment(['invoice_id' => Invoice::factory()->create(['total_amount' => 1000, 'subtotal' => 1000])->id, 'paid_at' => now(), 'amount' => 1001, 'payment_method' => 'Cash'], User::factory()->create());
    }

    public function test_only_pending_approval_can_receive_a_recorded_decision(): void
    {
        $administrator = User::factory()->create();
        $approval = Approval::factory()->create();

        app(ApprovalWorkflowService::class)->decide($approval, 'Approved', 'Reviewed and approved against the supporting record.', $administrator);

        $this->assertDatabaseHas('approvals', ['id' => $approval->id, 'status' => 'Approved', 'reviewer_id' => $administrator->id]);
        $this->expectException(ValidationException::class);
        app(ApprovalWorkflowService::class)->decide($approval, 'Rejected', 'A second decision must not replace the first.', $administrator);
    }

    public function test_audit_logs_are_immutable_and_financial_actions_are_recorded(): void
    {
        $administrator = User::factory()->create();
        $this->actingAs($administrator);
        Invoice::factory()->create();
        $log = AuditLog::query()->where('action', 'invoice_created')->firstOrFail();

        $this->expectException(LogicException::class);
        $log->delete();
    }
}
