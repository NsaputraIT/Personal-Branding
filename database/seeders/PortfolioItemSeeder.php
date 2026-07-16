<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;

class PortfolioItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'category' => 'web',
                'title' => 'Lumino App Design',
                'description' => 'Lumino is a sleek, user-friendly app design that combines modern aesthetics with intuitive functionality.',
                'image_path' => 'asset/img/portfolio/portfolio-1.webp',
                'detail_url' => null,
                'sort_order' => 1,
            ],
            [
                'category' => 'graphics',
                'title' => 'Brand Identity for Vividdy',
                'description' => 'Designed a bold visual identity that captures Vividdy\'s energetic and innovative spirit.',
                'image_path' => 'asset/img/portfolio/portfolio-2.webp',
                'detail_url' => null,
                'sort_order' => 2,
            ],
            [
                'category' => 'motion',
                'title' => 'Zelio Rebranding Motion',
                'description' => 'Produced a dynamic motion sequence for Zelio\'s rebrand, blending kinetic typography with 3D elements.',
                'image_path' => 'asset/img/portfolio/portfolio-3.webp',
                'detail_url' => null,
                'sort_order' => 3,
            ],
            [
                'category' => 'brand',
                'title' => 'Stellar Packaging Solutions',
                'description' => 'Crafted packaging that merges sustainability with premium feel for Stellar\'s product line.',
                'image_path' => 'asset/img/portfolio/portfolio-4.webp',
                'detail_url' => null,
                'sort_order' => 4,
            ],
            [
                'category' => 'web',
                'title' => 'E-Commerce Platform Redesign',
                'description' => 'Overhauled a legacy e-commerce platform with a modern, conversion-focused interface.',
                'image_path' => 'asset/img/portfolio/portfolio-5.webp',
                'detail_url' => null,
                'sort_order' => 5,
            ],
            [
                'category' => 'graphics',
                'title' => 'Editorial Layout Design',
                'description' => 'Designed an editorial layout that balances typographic harmony with visual storytelling.',
                'image_path' => 'asset/img/portfolio/portfolio-6.webp',
                'detail_url' => null,
                'sort_order' => 6,
            ],
        ];

        foreach ($items as $item) {
            PortfolioItem::create($item);
        }
    }
}
