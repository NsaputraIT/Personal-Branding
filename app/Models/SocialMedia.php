<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $site_id
 * @property string $medsos_icon
 * @property string $medsos_name
 * @property string $medsos_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Site $site
 */
#[Fillable(['site_id', 'medsos_icon', 'medsos_name', 'medsos_url'])]
class SocialMedia extends Model
{
    /**
     * Get the site that owns this social media record.
     *
     * @return BelongsTo<Site, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
