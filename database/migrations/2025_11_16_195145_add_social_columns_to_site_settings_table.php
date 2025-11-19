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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('social_twitter')->nullable()->after('contact_hero_text');
            $table->string('social_instagram')->nullable()->after('social_twitter');
            $table->string('social_youtube')->nullable()->after('social_instagram');
            $table->string('social_facebook')->nullable()->after('social_youtube');
        });

        DB::table('site_settings')->limit(1)->update([
            'social_twitter' => 'https://x.com/takvadergisi1',
            'social_instagram' => 'https://www.instagram.com/takvadergisi1/',
            'social_youtube' => 'https://www.youtube.com/@takvadergisi',
            'social_facebook' => 'https://www.facebook.com/profile.php?id=100064473440482',
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'social_twitter',
                'social_instagram',
                'social_youtube',
                'social_facebook',
            ]);
        });
    }
};
