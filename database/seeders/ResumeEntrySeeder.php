<?php

namespace Database\Seeders;

use App\Models\ResumeEntry;
use Illuminate\Database\Seeder;

class ResumeEntrySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // Work Experience
            [
                'type' => 'work',
                'institution' => 'Etiam Industries',
                'position' => 'Project Lead',
                'period_start' => 'Jun, 2023',
                'period_end' => 'Current',
                'description' => 'Qui deserunt veniam. Et sed aliquam labore tempore sed quisquam iusto autem sit. Ea vero voluptatum qui ut dignissimos deleniti nerada porti sand markend.',
                'bullet_points' => [],
                'sort_order' => 1,
            ],
            [
                'type' => 'work',
                'institution' => 'Nullam Corp',
                'position' => 'Senior graphic design specialist',
                'period_start' => '2019',
                'period_end' => '2023',
                'description' => 'Qui deserunt veniam. Et sed aliquam labore tempore sed quisquam iusto autem sit. Ea vero voluptatum qui ut dignissimos deleniti nerada porti sand markend.',
                'bullet_points' => [
                    'Lead in the design, development, and implementation of graphic layout and production materials',
                    'Delegate tasks to the 7 members of the design team and provide counsel on all aspects of the project.',
                    'Supervise the assessment of all graphic materials in order to ensure quality and accuracy of the design.',
                    'Oversee the efficient use of production project budgets ranging from $2,000 - $25,000.',
                ],
                'sort_order' => 2,
            ],
            [
                'type' => 'work',
                'institution' => 'Stepping Stone Ltd.',
                'position' => 'Graphic design specialist',
                'period_start' => '2015',
                'period_end' => '2019',
                'description' => 'Qui deserunt veniam. Et sed aliquam labore tempore sed quisquam iusto autem sit. Ea vero voluptatum qui ut dignissimos deleniti nerada porti sand markend.',
                'bullet_points' => [],
                'sort_order' => 3,
            ],
            // Education
            [
                'type' => 'education',
                'institution' => 'Vestibulum University',
                'position' => 'Diploma in Consequat',
                'period_start' => '2017',
                'period_end' => '2019',
                'description' => 'Qui deserunt veniam. Et sed aliquam labore tempore sed quisquam iusto autem sit. Ea vero voluptatum qui ut dignissimos deleniti nerada porti sand markend.',
                'bullet_points' => [],
                'sort_order' => 1,
            ],
            [
                'type' => 'education',
                'institution' => 'Nullam Corp',
                'position' => 'Master of Fine Arts & Graphic Design',
                'period_start' => '2019',
                'period_end' => '2023',
                'description' => 'Qui deserunt veniam. Et sed aliquam labore tempore sed quisquam iusto autem sit. Ea vero voluptatum qui ut dignissimos deleniti nerada porti sand markend.',
                'bullet_points' => [],
                'sort_order' => 2,
            ],
            [
                'type' => 'education',
                'institution' => 'Vestibulum University',
                'position' => 'Bachelor of Fine Arts & Graphic Design',
                'period_start' => '2015',
                'period_end' => '2019',
                'description' => 'Qui deserunt veniam. Et sed aliquam labore tempore sed quisquam iusto autem sit. Ea vero voluptatum qui ut dignissimos deleniti nerada porti sand markend.',
                'bullet_points' => [],
                'sort_order' => 3,
            ],
        ];

        foreach ($entries as $entry) {
            ResumeEntry::create($entry);
        }
    }
}
