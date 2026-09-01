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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique();
            $table->string('full_name_organization');
            $table->string('category');
            $table->string('contact_person')->nullable();
            $table->string('telephone', 30);
            $table->string('email')->nullable();
            $table->foreignId('district_id')->constrained()->restrictOnDelete();
            $table->foreignId('county_id')->constrained()->restrictOnDelete();
            $table->foreignId('sub_county_id')->constrained()->restrictOnDelete();
            $table->foreignId('parish_id')->constrained()->restrictOnDelete();
            $table->foreignId('village_id')->constrained()->restrictOnDelete();
            $table->string('physical_address_landmark', 500)->nullable();
            $table->string('current_property_allocation')->nullable();
            $table->string('agreement_reference')->nullable();
            $table->string('billing_cycle')->nullable();
            $table->decimal('opening_balance', 18, 2)->nullable();
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->string('record_status');
            $table->string('responsible_unit')->nullable();
            $table->string('record_owner')->nullable();
            $table->text('administrative_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['campus_id', 'record_status']);
            $table->index(['category', 'record_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
