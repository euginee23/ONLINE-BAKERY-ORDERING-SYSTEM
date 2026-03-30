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
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->string('house_street')->after('label');
            $table->string('barangay')->after('house_street');
            $table->string('city')->after('barangay');
            $table->string('province')->after('city');
            $table->string('region', 100)->nullable()->after('province');
            $table->string('zip_code', 10)->after('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['house_street', 'barangay', 'city', 'province', 'region', 'zip_code']);
            $table->text('address')->after('label');
        });
    }
};
