<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SkillSeeder::class,
            ServiceSeeder::class,
            PortfolioItemSeeder::class,
            TestimonialSeeder::class,
            FaqItemSeeder::class,
            ResumeEntrySeeder::class,
            SectionMetadataSeeder::class,
        ]);
    }
}
