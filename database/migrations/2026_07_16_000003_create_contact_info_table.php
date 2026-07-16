<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_info', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->text('subheading')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('map_url')->nullable();
            $table->string('map_text')->nullable();
            $table->timestamps();
        });

        DB::table('contact_info')->insert([
            'heading' => 'Contact',
            'subheading' => 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit',
            'email' => 'info@example.com',
            'phone' => '+1 5589 55488 558',
            'address' => 'A108 Adam Street, New York, NY 535022',
            'map_url' => '#',
            'map_text' => 'Open Map',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_info');
    }
};
