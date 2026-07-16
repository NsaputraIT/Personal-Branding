<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_entries', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'work' or 'education'
            $table->string('institution');
            $table->string('position');
            $table->string('period_start')->nullable();
            $table->string('period_end')->nullable();
            $table->text('description')->nullable();
            $table->json('bullet_points')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_entries');
    }
};
