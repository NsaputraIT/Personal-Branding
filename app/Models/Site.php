<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $site_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SocialMedia> $socialMedia
 */
#[Fillable(['site_name'])]
class Site extends Model
{
    /**
     * Get the social media records for this site.
     *
     * @return HasMany<SocialMedia, $this>
     */
    public function socialMedia(): HasMany
    {
        return $this->hasMany(SocialMedia::class);
    }
}
