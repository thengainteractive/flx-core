<?php

namespace Modules\Venue\Models;

use App\Models\Traits\HasAuthors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueSpace extends Model
{
    use HasAuthors;

    protected $fillable = [
        'venue_id',
        'name',
        'type',
        'capacity',
        'area_sq_ft',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
