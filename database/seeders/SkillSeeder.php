<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            [
                'name' => 'Laravel',
                'description' => 'Expert-level PHP framework development with Livewire, Eloquent, and ecosystem tools.',
                'percentage' => 95,
                'sort_order' => 1,
            ],
            [
                'name' => 'PHP',
                'description' => 'Deep understanding of modern PHP 8.x features, OOP patterns, and performance optimization.',
                'percentage' => 90,
                'sort_order' => 2,
            ],
            [
                'name' => 'MySQL',
                'description' => 'Database design, query optimization, indexing strategies, and relational modeling.',
                'percentage' => 90,
                'sort_order' => 3,
            ],
            [
                'name' => 'JavaScript',
                'description' => 'Modern ES6+ development with Alpine.js, Livewire interactions, and frontend tooling.',
                'percentage' => 85,
                'sort_order' => 4,
            ],
            [
                'name' => 'Flutter',
                'description' => 'Cross-platform mobile development with Dart, state management, and native integrations.',
                'percentage' => 80,
                'sort_order' => 5,
            ],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['name' => $skill['name']],
                [
                    'description' => $skill['description'],
                    'percentage' => $skill['percentage'],
                    'sort_order' => $skill['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
