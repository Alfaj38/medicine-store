<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Traits\BelongsToTenant;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Item extends Model implements HasMedia
{
    use BelongsToTenant, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'track_batch' => 'boolean',
        'track_expiry' => 'boolean',
        'track_serial' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ItemBrand::class);
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function secondaryUnit()
    {
        return $this->belongsTo(Uom::class, 'secondary_unit_id');
    }

    public function taxCategory(): BelongsTo
    {
        return $this->belongsTo(TaxCategory::class);
    }

    public function medicineDetails(): HasOne
    {
        return $this->hasOne(ItemMedicineDetail::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function barcodes(): HasMany
    {
        return $this->hasMany(ItemBarcode::class);
    }

    public function uoms(): HasMany
    {
        return $this->hasMany(ItemUom::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ItemAttributeValue::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'medicine_id'); // mapping to the legacy column medicine_id
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'medicine_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(ItemUnit::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('optimized')
              ->format('webp')
              ->quality(80);
    }
}
