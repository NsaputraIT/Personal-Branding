<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('copyright_text')->nullable();
            $table->string('credit_text')->nullable();
            $table->string('credit_url')->nullable();
            $table->timestamps();
        });

        DB::table('footer_settings')->insert([
            'copyright_text' => 'EasyFolio',
            'credit_text' => 'Designed by BootstrapMade',
            'credit_url' => 'https://bootstrapmade.com/',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
