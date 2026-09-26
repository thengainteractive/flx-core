<?php

namespace Modules\Venue\Models;

use App\Models\Traits\HasAuthors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Venue extends Model implements HasMedia
{
    use HasAuthors, InteractsWithMedia;

    protected $fillable = [
        'location_id',
        'venue_type_id',
        'name',
        'slug',
        'description',
        'tagline',
        'city_area',
        'price',
        'pricing_unit',
        'rating',
        'review_count',
        'max_floating_capacity',
        'max_seated_capacity',
        'rooms_available',
        'total_area_sq_ft',
        'parking_spots',
        'catering_policy',
        'alcohol_policy',
        'highlights',
        'amenities',
        'rules',
        'verified',
    ];

    protected $casts = [
        'highlights' => 'array',
        'amenities' => 'array',
        'rules' => 'array',
        'verified' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function venueType(): BelongsTo
    {
        return $this->belongsTo(VenueType::class);
    }

    public function spaces(): HasMany
    {
        return $this->hasMany(VenueSpace::class);
    }
}
