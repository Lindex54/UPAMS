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
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('category');
            $table->string('registration_number', 100)->nullable()->after('company_name');
            $table->string('tin', 30)->nullable()->after('registration_number');
            $table->string('tax_returns')->nullable()->after('tin');
            $table->text('director_names')->nullable()->after('tax_returns');
            $table->text('owner_names')->nullable()->after('director_names');
            $table->string('local_council_permit')->nullable()->after('owner_names');
            $table->index('registration_number');
            $table->index('tin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropIndex(['registration_number']);
            $table->dropIndex(['tin']);
            $table->dropColumn(['company_name', 'registration_number', 'tin', 'tax_returns', 'director_names', 'owner_names', 'local_council_permit']);
        });
    }
};
