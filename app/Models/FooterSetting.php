<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $copyright_text
 * @property string|null $credit_text
 * @property string|null $credit_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'copyright_text',
    'credit_text',
    'credit_url',
])]
class FooterSetting extends Model {}
