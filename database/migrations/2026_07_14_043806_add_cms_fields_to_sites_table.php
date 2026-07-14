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
        Schema::table('sites', function (Blueprint $table) {
            $table->json('hero_data')->nullable()->after('site_name');
            $table->json('about_data')->nullable()->after('hero_data');
            $table->json('contact_data')->nullable()->after('about_data');
            $table->json('footer_data')->nullable()->after('contact_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'hero_data',
                'about_data',
                'contact_data',
                'footer_data',
            ]);
        });
    }
};