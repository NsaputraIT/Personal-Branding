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
            ['name' => 'Laravel', 'percentage' => 95, 'sort_order' => 1],
            ['name' => 'PHP', 'percentage' => 90, 'sort_order' => 2],
            ['name' => 'MySQL', 'percentage' => 90, 'sort_order' => 3],
            ['name' => 'JavaScript', 'percentage' => 85, 'sort_order' => 4],
            ['name' => 'Flutter', 'percentage' => 80, 'sort_order' => 5],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['name' => $skill['name']],
                [
                    'percentage' => $skill['percentage'],
                    'sort_order' => $skill['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}