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
        Schema::table('campuses', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('name');
            $table->foreignId('district_id')->nullable()->after('code')->constrained()->nullOnDelete();
            $table->string('location')->nullable()->after('district_id');
            $table->string('faculty')->nullable()->after('location');
            $table->decimal('latitude', 10, 7)->nullable()->after('faculty');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('status')->default('Active')->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('district_id');
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'location', 'faculty', 'latitude', 'longitude', 'status']);
        });
    }
};
