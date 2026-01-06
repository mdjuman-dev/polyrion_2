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
        Schema::table('user_kyc_verifications', function (Blueprint $table) {
            $table->date('passport_expiry_date')->nullable()->after('passport_biodata_photo');
            $table->string('passport_cover_photo')->nullable()->after('passport_expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_kyc_verifications', function (Blueprint $table) {
            $table->dropColumn(['passport_expiry_date', 'passport_cover_photo']);
        });
    }
};
