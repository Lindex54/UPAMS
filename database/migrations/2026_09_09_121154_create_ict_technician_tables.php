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
        Schema::create('computer_labs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->foreignId('org_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('building');
            $table->string('room');
            $table->unsignedInteger('capacity')->default(0);
            $table->foreignId('responsible_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['campus_id', 'building', 'room']);
            $table->index(['campus_id', 'org_unit_id']);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->boolean('is_ict')->default(false)->after('name')->index();
            $table->string('serial_number')->nullable()->unique()->after('reference');
            $table->string('make')->nullable()->after('name');
            $table->string('model')->nullable()->after('make');
            $table->string('processor')->nullable()->after('model');
            $table->string('ram')->nullable()->after('processor');
            $table->string('storage')->nullable()->after('ram');
            $table->foreignId('computer_lab_id')->nullable()->after('org_unit_id')->constrained()->nullOnDelete();
            $table->string('building')->nullable()->after('computer_lab_id');
            $table->string('room')->nullable()->after('building');
            $table->string('custodian')->nullable()->after('room');
            $table->string('operational_status')->default('Operational')->after('condition');
            $table->decimal('purchase_cost', 15, 2)->nullable()->after('acquired_at');
            $table->string('supplier')->nullable()->after('purchase_cost');
            $table->date('warranty_expires_at')->nullable()->after('supplier');
            $table->index(['computer_lab_id', 'operational_status']);
            $table->index(['campus_id', 'is_ict']);
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreignId('assigned_technician_id')->nullable()->after('org_unit_id')->constrained('users')->nullOnDelete();
            $table->text('fault_description')->nullable()->after('title');
            $table->text('diagnosis')->nullable()->after('fault_description');
            $table->text('repair_notes')->nullable()->after('diagnosis');
            $table->dateTime('reported_at')->nullable()->after('status');
            $table->dateTime('diagnosed_at')->nullable()->after('reported_at');
            $table->dateTime('repair_started_at')->nullable()->after('diagnosed_at');
            $table->dateTime('testing_started_at')->nullable()->after('repair_started_at');
            $table->dateTime('resolved_at')->nullable()->after('testing_started_at');
            $table->index(['assigned_technician_id', 'status']);
        });

        Schema::create('ict_equipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('computer_lab_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custodian')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at');
            $table->dateTime('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'returned_at']);
        });

        Schema::create('ict_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('computer_lab_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('condition');
            $table->string('operational_status');
            $table->text('findings');
            $table->dateTime('inspected_at');
            $table->date('next_due_at')->nullable();
            $table->timestamps();
            $table->index(['technician_id', 'inspected_at']);
        });

        Schema::create('ict_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_campus_id')->constrained('campuses')->restrictOnDelete();
            $table->foreignId('to_campus_id')->constrained('campuses')->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('Requested');
            $table->text('reason');
            $table->dateTime('requested_at');
            $table->timestamps();
            $table->index(['from_campus_id', 'status']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('related_reference');
            $table->foreignId('asset_id')->nullable()->after('file_path')->constrained()->nullOnDelete();
            $table->index(['asset_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropIndex(['asset_id', 'document_type']);
            $table->dropColumn(['file_path', 'asset_id']);
        });
        Schema::dropIfExists('ict_transfer_requests');
        Schema::dropIfExists('ict_inspections');
        Schema::dropIfExists('ict_equipment_assignments');
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_technician_id']);
            $table->dropIndex(['assigned_technician_id', 'status']);
            $table->dropColumn(['assigned_technician_id', 'fault_description', 'diagnosis', 'repair_notes', 'reported_at', 'diagnosed_at', 'repair_started_at', 'testing_started_at', 'resolved_at']);
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['computer_lab_id']);
            $table->dropUnique(['serial_number']);
            $table->dropIndex(['computer_lab_id', 'operational_status']);
            $table->dropIndex(['campus_id', 'is_ict']);
            $table->dropIndex(['is_ict']);
            $table->dropColumn(['is_ict', 'serial_number', 'make', 'model', 'processor', 'ram', 'storage', 'computer_lab_id', 'building', 'room', 'custodian', 'operational_status', 'purchase_cost', 'supplier', 'warranty_expires_at']);
        });
        Schema::dropIfExists('computer_labs');
    }
};
