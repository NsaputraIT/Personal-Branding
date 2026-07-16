<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Saul Goodman',
                'role' => 'Client',
                'quote_heading' => 'Exceptional Work!',
                'quote_paragraphs' => [
                    'Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.',
                    'Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.',
                ],
                'avatar_path' => 'asset/img/person/person-1.webp',
                'featured_image_path' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Sara Wilsson',
                'role' => 'Designer',
                'quote_heading' => 'Outstanding Quality',
                'quote_paragraphs' => [
                    'Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.',
                    'Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.',
                ],
                'avatar_path' => 'asset/img/person/person-2.webp',
                'featured_image_path' => null,
                'sort_order' => 2,
            ],
            [
                'name' => 'Matt Brandon',
                'role' => 'Freelancer',
                'quote_heading' => 'Highly Recommend',
                'quote_paragraphs' => [
                    'Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim fugiat minim velit minim dolor enim duis veniam ipsum anim magna sunt elit fore quem dolore labore illum veniam.',
                ],
                'avatar_path' => 'asset/img/person/person-3.webp',
                'featured_image_path' => null,
                'sort_order' => 3,
            ],
            [
                'name' => 'Jena Karlis',
                'role' => 'Store Owner',
                'quote_heading' => 'Fantastic Results',
                'quote_paragraphs' => [
                    'Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore quis sint minim.',
                ],
                'avatar_path' => 'asset/img/person/person-4.webp',
                'featured_image_path' => null,
                'sort_order' => 4,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
