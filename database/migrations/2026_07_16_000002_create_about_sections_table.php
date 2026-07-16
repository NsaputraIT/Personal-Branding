<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->text('description')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('paragraph1')->nullable();
            $table->text('paragraph2')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('signature_image')->nullable();
            $table->string('signature_name')->nullable();
            $table->string('signature_title')->nullable();
            $table->json('info_items')->nullable();
            $table->timestamps();
        });

        DB::table('about_sections')->insert([
            'heading' => 'About',
            'description' => 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit',
            'subtitle' => 'About Me',
            'paragraph1' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'paragraph2' => 'Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit.',
            'profile_image' => 'asset/img/profile/profile-square-2.webp',
            'signature_image' => 'asset/img/misc/signature-1.webp',
            'signature_name' => 'Eliot Johnson',
            'signature_title' => 'Adipiscing Elit, Lorem Ipsum',
            'info_items' => json_encode([
                ['label' => 'Name', 'value' => 'Eliot Johnson'],
                ['label' => 'Phone', 'value' => '+123 456 7890'],
                ['label' => 'Age', 'value' => '26 Years'],
                ['label' => 'Email', 'value' => 'email@example.com'],
                ['label' => 'Occupation', 'value' => 'Lorem Engineer'],
                ['label' => 'Nationality', 'value' => 'Ipsum'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('about_sections');
    }
};
