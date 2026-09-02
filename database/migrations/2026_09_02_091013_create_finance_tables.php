<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('beneficiary_id')->constrained()->restrictOnDelete();
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->string('property_reference')->nullable();
            $table->string('asset_reference')->nullable();
            $table->string('agreement_reference')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->text('description');
            $table->decimal('subtotal', 18, 2);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2);
            $table->string('status')->default('Issued');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['campus_id', 'status']);
            $table->index(['beneficiary_id', 'due_date']);
            $table->index(['status', 'due_date']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('invoice_id')->constrained()->restrictOnDelete();
            $table->foreignId('beneficiary_id')->constrained()->restrictOnDelete();
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->dateTime('paid_at');
            $table->decimal('amount', 18, 2);
            $table->string('payment_method');
            $table->string('payment_reference')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->string('status')->default('Recorded');
            $table->text('reversal_reason')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['invoice_id', 'status']);
            $table->index(['campus_id', 'paid_at']);
            $table->index(['beneficiary_id', 'paid_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
