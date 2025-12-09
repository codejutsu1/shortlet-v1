<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\PropertyCacheService;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'address',
        'city',
        'state',
        'price_per_night',
        'bedrooms',
        'bathrooms',
        'max_guests',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'max_guests' => 'integer',
        'is_featured' => 'boolean',
    ];

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Model Events

    protected static function booted()
    {
        // Clear property cache when properties are created, updated, or deleted
        static::saved(function () {
            app(PropertyCacheService::class)->clearPropertyListCache();
        });

        static::deleted(function () {
            app(PropertyCacheService::class)->clearPropertyListCache();
        });
    }

    // Query Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }
}
