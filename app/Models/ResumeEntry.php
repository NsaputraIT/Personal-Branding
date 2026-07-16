<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $type
 * @property string $institution
 * @property string $position
 * @property string|null $period_start
 * @property string|null $period_end
 * @property string|null $description
 * @property array<int, string>|null $bullet_points
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'type',
    'institution',
    'position',
    'period_start',
    'period_end',
    'description',
    'bullet_points',
    'sort_order',
    'is_active',
])]
class ResumeEntry extends Model
{
    protected function casts(): array
    {
        return [
            'bullet_points' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
