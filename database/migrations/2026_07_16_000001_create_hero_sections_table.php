<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->text('subheading')->nullable();
            $table->string('cta_primary_text')->nullable();
            $table->string('cta_primary_url')->nullable();
            $table->string('cta_secondary_text')->nullable();
            $table->string('cta_secondary_url')->nullable();
            $table->string('profile_image')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
        });

        DB::table('hero_sections')->insert([
            'heading' => 'Crafting Digital Experiences with Passion',
            'subheading' => 'Transforming ideas into elegant solutions through creative design and innovative development',
            'cta_primary_text' => 'View My Work',
            'cta_primary_url' => '#portfolio',
            'cta_secondary_text' => "Let's Connect",
            'cta_secondary_url' => '#contact',
            'profile_image' => 'asset/img/profile/profile-1.webp',
            'stats' => json_encode([
                ['number' => '5+', 'label' => 'Years Experience'],
                ['number' => '100+', 'label' => 'Projects Completed'],
                ['number' => '50+', 'label' => 'Happy Clients'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_sections');
    }
};
