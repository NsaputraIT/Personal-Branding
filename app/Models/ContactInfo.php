<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $heading
 * @property string|null $subheading
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $map_url
 * @property string|null $map_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'heading',
    'subheading',
    'email',
    'phone',
    'address',
    'map_url',
    'map_text',
])]
class ContactInfo extends Model
{
    protected $table = 'contact_info';
}
