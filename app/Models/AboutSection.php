<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $heading
 * @property string|null $description
 * @property string|null $subtitle
 * @property string|null $paragraph1
 * @property string|null $paragraph2
 * @property string|null $profile_image
 * @property string|null $signature_image
 * @property string|null $signature_name
 * @property string|null $signature_title
 * @property array<int, array{label: string, value: string}>|null $info_items
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'heading',
    'description',
    'subtitle',
    'paragraph1',
    'paragraph2',
    'profile_image',
    'signature_image',
    'signature_name',
    'signature_title',
    'info_items',
])]
class AboutSection extends Model
{
    protected function casts(): array
    {
        return [
            'info_items' => 'array',
        ];
    }
}
