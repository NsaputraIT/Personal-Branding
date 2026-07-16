<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SectionMetadataSeeder extends Seeder
{
    /**
     * Seed section-level headings and descriptions for multi-record sections.
     */
    public function run(): void
    {
        $site = Site::first();

        if (! $site) {
            return;
        }

        $site->section_metadata = [
            'skills' => [
                'title' => 'Skills',
                'description' => 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit',
            ],
            'resume' => [
                'title' => 'Resume',
                'description' => 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit',
            ],
            'portfolio' => [
                'title' => 'Portfolio',
                'description' => 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit',
            ],
            'testimonials' => [
                'title' => 'Testimonials',
                'description' => 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit',
            ],
            'services' => [
                'title' => 'Services',
                'description' => 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur vel illum qui dolorem',
                'sidebar_heading' => 'Consectetur adipiscing elit sed do eiusmod tempor',
                'sidebar_text' => 'Nulla metus metus ullamcorper vel tincidunt sed euismod nibh volutpat velit class aptent taciti sociosqu ad litora.',
                'sidebar_cta' => 'See all services',
            ],
            'faq' => [
                'title' => 'Frequently Asked Questions',
                'description' => 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit',
            ],
        ];

        $site->save();
    }
}
