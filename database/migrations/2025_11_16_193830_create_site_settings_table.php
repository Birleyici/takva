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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_address', 255)->nullable();
            $table->text('contact_map_embed')->nullable();
            $table->text('contact_hero_text')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_whatsapp')->nullable();
            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            'contact_email' => 'takvasorucevap@gmail.com',
            'contact_phone' => '05528227442',
            'contact_address' => 'Tatlıcak Mh. Uluyatır Sk. No:42/A Karatay / KONYA',
            'contact_map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3149.156453270041!2d32.55908747641665!3d37.880023706089375!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14d09b0075c61bc1%3A0xcc7271a74eb4ea46!2sTakva%20Dergisi!5e0!3m2!1str!2str!4v1750237294648',
            'contact_hero_text' => 'Görüş, öneri ve katkılarınızı memnuniyetle dinliyoruz. Aşağıdaki kanallardan ekibimize ulaşabilirsiniz.',
            'social_twitter' => 'https://x.com/takvadergisi1',
            'social_instagram' => 'https://www.instagram.com/takvadergisi1/',
            'social_youtube' => 'https://www.youtube.com/@takvadergisi',
            'social_facebook' => 'https://www.facebook.com/profile.php?id=100064473440482',
            'social_whatsapp' => '+90 552 822 74 42',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
