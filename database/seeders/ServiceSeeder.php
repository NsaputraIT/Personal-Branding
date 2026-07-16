<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'icon' => 'bi-activity',
                'title' => 'Nesciunt Mete',
                'description' => 'Provident nihil minus qui consequatur non omnis maiores. Eos accusantium minus dolores iure perferendis tempore et consequatur.',
                'sort_order' => 1,
            ],
            [
                'icon' => 'bi-easel',
                'title' => 'Eosle Commodi',
                'description' => 'Ut autem aut autem non a. Sint sint sit facilis nam iusto sint. Libero corrupti neque eum hic non ut nesciunt dolorem.',
                'sort_order' => 2,
            ],
            [
                'icon' => 'bi-broadcast',
                'title' => 'Ledo Markt',
                'description' => 'Ut excepturi voluptatem nisi sed. Quidem fuga consequatur. Minus ea aut. Vel qui id eveniet ratione ut commodi rerum.',
                'sort_order' => 3,
            ],
            [
                'icon' => 'bi-bounding-box-circles',
                'title' => 'Asperiores Commodit',
                'description' => 'Non et temporibus minus omnis sed dolor esse consequatur. Cupiditate sed error ea fuga sit provident adipisci neque.',
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
