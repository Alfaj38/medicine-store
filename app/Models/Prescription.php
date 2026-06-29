<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToTenant;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Prescription extends Model implements HasMedia
{
    use BelongsToTenant, InteractsWithMedia;

    protected $guarded = [];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('optimized')
              ->format('webp')
              ->quality(80);
    }
}
