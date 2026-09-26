<?php

namespace Modules\Venue\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'venue_locations';

    protected $fillable = [
        'city',
        'district',
        'state',
        'slug',
    ];

    public function getFullNameAttribute(): string
    {
        return collect([$this->city, $this->district, $this->state])->filter()->join(', ');
    }
}
