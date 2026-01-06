<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->unique();
            $table->string('url')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });

        $platforms = ['facebook', 'twitter', 'instagram', 'telegram', 'whatsapp', 'youtube', 'linkedin'];
        foreach ($platforms as $platform) {
            DB::table('social_media_links')->insert([
                'platform' => $platform,
                'url' => null,
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_links');
    }
};
