<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'question' => 'Non consectetur a erat nam at lectus urna duis?',
                'answer' => 'Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Feugiat scelerisque varius morbi enim nunc faucibus a pellentesque?',
                'answer' => 'Dolor sit amet consectetur adipiscing elit ut aliquam purus. Nec sagittis aliquam malesuada bibendum arcu vitae elementum curabitur vitae. Nunc sed id semper risus in hendrerit gravida rutrum quisque.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi?',
                'answer' => 'Eleifend mi in nulla posuere sollicitudin aliquam ultrices sagittis orci. Faucibus pulvinar elementum integer enim neque volutpat ac tincidunt vitae. Mauris augue neque gravida in fermentum et sollicitudin ac orci.',
                'sort_order' => 3,
            ],
            [
                'question' => 'Ac odio tempor orci dapibus ultrices in iaculis nunc sed augue?',
                'answer' => 'Amet volutpat consequat mauris nunc congue nisi vitae suscipit tellus. Lacus viverra vitae congue eu consequat ac felis donec et odio pellentesque diam volutpat commodo sed egestas egestas.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Tempus quam pellentesque nec nam aliquam sem et tortor consequat?',
                'answer' => 'Molestie a iaculis at erat pellentesque adipiscing commodo elit at imperdiet dui accumsan sit amet nulla facilisi morbi tempus iaculis urna id volutpat lacus laoreet non curabitur gravida.',
                'sort_order' => 5,
            ],
            [
                'question' => 'Perspiciatis quod quia ea minus autem qui tempora?',
                'answer' => 'Sit amet est placerat in egestas erat imperdiet sed euismod nisi porta lorem mollis aliquam ut porttitor leo a diam sollicitudin tempor id eu nisl nunc mi ipsum faucibus scelerisque eleifend donec pretium vulputate sapien nec sagittis aliquam.',
                'sort_order' => 6,
            ],
        ];

        foreach ($items as $item) {
            FaqItem::create($item);
        }
    }
}
