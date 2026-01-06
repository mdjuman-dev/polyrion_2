<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('id_type', ['NID', 'Driving License', 'Passport']);
            $table->string('nid_front_photo')->nullable();
            $table->string('nid_back_photo')->nullable();
            $table->string('license_number')->nullable();
            $table->string('full_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('license_front_photo')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_biodata_photo')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_kyc_verifications');
    }
};
