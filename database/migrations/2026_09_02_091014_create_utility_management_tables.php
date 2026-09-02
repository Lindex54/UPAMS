<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('utility_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('unit');
            $table->decimal('default_rate', 18, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $timestamp = now();
        DB::table('utility_types')->insert([
            ['name' => 'Electricity', 'unit' => 'kWh', 'default_rate' => 0, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Water', 'unit' => 'm³', 'default_rate' => 0, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Gas', 'unit' => 'm³', 'default_rate' => 0, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Internet', 'unit' => 'month', 'default_rate' => 0, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Waste', 'unit' => 'month', 'default_rate' => 0, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);

        Schema::create('utility_meters', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('utility_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->string('property_reference')->nullable();
            $table->string('meter_number')->unique();
            $table->string('unit');
            $table->decimal('rate', 18, 4);
            $table->decimal('abnormal_threshold', 18, 4)->nullable();
            $table->string('status')->default('Active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['campus_id', 'status']);
            $table->index(['utility_type_id', 'status']);
        });

        Schema::create('utility_billings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('utility_meter_id')->constrained()->restrictOnDelete();
            $table->foreignId('utility_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->date('billing_period');
            $table->decimal('previous_reading', 18, 4);
            $table->decimal('current_reading', 18, 4);
            $table->decimal('consumption', 18, 4);
            $table->decimal('rate', 18, 4);
            $table->decimal('charge', 18, 2);
            $table->string('payer_name');
            $table->string('payment_status')->default('Unpaid');
            $table->boolean('is_abnormal')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['utility_meter_id', 'billing_period']);
            $table->index(['campus_id', 'payment_status']);
            $table->index(['is_abnormal', 'billing_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_billings');
        Schema::dropIfExists('utility_meters');
        Schema::dropIfExists('utility_types');
    }
};
