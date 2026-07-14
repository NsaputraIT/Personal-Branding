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
        Schema::create('social_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('medsos_icon');
            $table->string('medsos_name');
            $table->string('medsos_url');
            $table->timestamps();
        });

        // Seed initial social media records for the first site
        // medsos_icon stores a platform identifier; the display name and
        // Bootstrap icon class are resolved from a mapping inside the app.
        DB::table('social_media')->insert([
            ['site_id' => 1, 'medsos_icon' => 'twitter', 'medsos_name' => 'Twitter/X', 'medsos_url' => '#', 'created_at' => now(), 'updated_at' => now()],
            ['site_id' => 1, 'medsos_icon' => 'facebook', 'medsos_name' => 'Facebook', 'medsos_url' => '#', 'created_at' => now(), 'updated_at' => now()],
            ['site_id' => 1, 'medsos_icon' => 'instagram', 'medsos_name' => 'Instagram', 'medsos_url' => '#', 'created_at' => now(), 'updated_at' => now()],
            ['site_id' => 1, 'medsos_icon' => 'linkedin', 'medsos_name' => 'LinkedIn', 'medsos_url' => '#', 'created_at' => now(), 'updated_at' => now()],
            ['site_id' => 1, 'medsos_icon' => 'tiktok', 'medsos_name' => 'TikTok', 'medsos_url' => '#', 'created_at' => now(), 'updated_at' => now()],
            ['site_id' => 1, 'medsos_icon' => 'blog', 'medsos_name' => 'Blog', 'medsos_url' => '#', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media');
    }
};
