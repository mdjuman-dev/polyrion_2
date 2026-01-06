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
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_verification_type')->nullable()->after('withdrawal_password');
            $table->string('id_license_number')->nullable()->after('id_verification_type');
            $table->string('id_passport_number')->nullable()->after('id_license_number');
            $table->string('id_full_name')->nullable()->after('id_passport_number');
            $table->date('id_date_of_birth')->nullable()->after('id_full_name');
            $table->string('id_front_photo')->nullable()->after('id_date_of_birth');
            $table->string('id_back_photo')->nullable()->after('id_front_photo');
            $table->string('id_biodata_photo')->nullable()->after('id_back_photo');
            $table->string('id_verification_status')->default('pending')->after('id_biodata_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'id_verification_type',
                'id_license_number',
                'id_passport_number',
                'id_full_name',
                'id_date_of_birth',
                'id_front_photo',
                'id_back_photo',
                'id_biodata_photo',
                'id_verification_status',
            ]);
        });
    }
};
