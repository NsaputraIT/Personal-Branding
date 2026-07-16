<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $role
 * @property string|null $quote_heading
 * @property array<int, string> $quote_paragraphs
 * @property string $avatar_path
 * @property string|null $featured_image_path
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name',
    'role',
    'quote_heading',
    'quote_paragraphs',
    'avatar_path',
    'featured_image_path',
    'sort_order',
    'is_active',
])]
class Testimonial extends Model
{
    protected function casts(): array
    {
        return [
            'quote_paragraphs' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
