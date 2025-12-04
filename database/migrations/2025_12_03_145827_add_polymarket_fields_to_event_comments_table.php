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
        Schema::table('event_comments', function (Blueprint $table) {
            // Polymarket API fields
            $table->string('polymarket_id')->nullable()->unique()->after('id');
            $table->string('user_address')->nullable()->after('user_id');
            $table->string('reply_address')->nullable()->after('user_address');
            $table->string('parent_entity_type')->nullable()->after('reply_address');
            $table->integer('parent_entity_id')->nullable()->after('parent_entity_type');
            $table->string('parent_comment_polymarket_id')->nullable()->after('parent_comment_id');
            $table->json('reactions')->nullable()->after('replies_count');
            $table->integer('reaction_count')->default(0)->after('reactions');
            $table->integer('report_count')->default(0)->after('reaction_count');
            $table->json('profile_data')->nullable()->after('report_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_comments', function (Blueprint $table) {
            $table->dropColumn([
                'polymarket_id',
                'user_address',
                'reply_address',
                'parent_entity_type',
                'parent_entity_id',
                'parent_comment_polymarket_id',
                'reactions',
                'reaction_count',
                'report_count',
                'profile_data'
            ]);
        });
    }
};
