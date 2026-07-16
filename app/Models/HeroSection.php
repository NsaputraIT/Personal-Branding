<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $heading
 * @property string|null $subheading
 * @property string|null $cta_primary_text
 * @property string|null $cta_primary_url
 * @property string|null $cta_secondary_text
 * @property string|null $cta_secondary_url
 * @property string|null $profile_image
 * @property array<int, array{number: string, label: string}>|null $stats
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'heading',
    'subheading',
    'cta_primary_text',
    'cta_primary_url',
    'cta_secondary_text',
    'cta_secondary_url',
    'profile_image',
    'stats',
])]
class HeroSection extends Model
{
    protected function casts(): array
    {
        return [
            'stats' => 'array',
        ];
    }
}
