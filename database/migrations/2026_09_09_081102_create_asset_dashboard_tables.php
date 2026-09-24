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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['asset_category_id', 'name']);
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('name');
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('org_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asset_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('asset_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('Active');
            $table->string('condition')->default('Good');
            $table->date('acquired_at')->nullable();
            $table->date('next_service_at')->nullable();
            $table->date('calibration_due_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['campus_id', 'status']);
            $table->index(['org_unit_id', 'condition']);
            $table->index(['asset_type_id', 'created_at']);
        });

        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title');
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('org_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('Draft');
            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['campus_id', 'status']);
            $table->index(['status', 'expires_at']);
        });

        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title');
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('org_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Problem Reported');
            $table->date('target_completion_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['campus_id', 'status']);
            $table->index(['priority', 'status']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title');
            $table->string('document_type');
            $table->string('related_reference')->nullable();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('org_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('Current');
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['campus_id', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('agreements');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_types');
        Schema::dropIfExists('asset_categories');
    }
};
