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
        Schema::table('ict_equipment_assignments', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });
        Schema::table('ict_inspections', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('next_due_at')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });
        Schema::table('ict_transfer_requests', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('requested_at')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('user_role')->nullable()->after('user_name');
        });

        DB::table('ict_equipment_assignments')->update(['created_by' => DB::raw('assigned_by'), 'updated_by' => DB::raw('assigned_by')]);
        DB::table('ict_inspections')->update(['created_by' => DB::raw('technician_id'), 'updated_by' => DB::raw('technician_id')]);
        DB::table('ict_transfer_requests')->update(['created_by' => DB::raw('requested_by'), 'updated_by' => DB::raw('requested_by')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn('user_role');
        });
        Schema::table('ict_transfer_requests', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
        Schema::table('ict_inspections', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
        Schema::table('ict_equipment_assignments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
