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
            $table->text('nin')->nullable()->after('email');
            $table->char('nin_hash', 64)->nullable()->unique()->after('nin');
            $table->string('national_id_given_names')->after('nin_hash');
            $table->string('national_id_surname')->after('national_id_given_names');
            $table->string('national_id_sex', 20)->nullable()->after('national_id_surname');
            $table->string('nationality', 100)->nullable()->after('national_id_sex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropUnique(['nin_hash']);
            $table->dropColumn([
                'nin',
                'nin_hash',
                'national_id_given_names',
                'national_id_surname',
                'national_id_sex',
                'nationality',
            ]);
        });
    }
};
