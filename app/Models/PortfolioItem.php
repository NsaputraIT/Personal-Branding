<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $category
 * @property string $title
 * @property string $description
 * @property string $image_path
 * @property string|null $detail_url
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'category',
    'title',
    'description',
    'image_path',
    'detail_url',
    'sort_order',
    'is_active',
])]
class PortfolioItem extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
