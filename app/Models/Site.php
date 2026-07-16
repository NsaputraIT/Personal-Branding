<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $site_name
 * @property array<string, mixed>|null $hero_data
 * @property array<string, mixed>|null $about_data
 * @property array<string, mixed>|null $contact_data
 * @property array<string, mixed>|null $footer_data
 * @property array<string, mixed>|null $section_metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, SocialMedia> $socialMedia
 */
#[Fillable(['site_name'])]
class Site extends Model
{
    protected function casts(): array
    {
        return [
            'hero_data' => 'array',
            'about_data' => 'array',
            'contact_data' => 'array',
            'footer_data' => 'array',
            'section_metadata' => 'array',
        ];
    }

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
