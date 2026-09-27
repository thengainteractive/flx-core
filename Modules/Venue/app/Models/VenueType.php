<?php

namespace Modules\Venue\Models;

use Illuminate\Database\Eloquent\Model;

class VenueType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
    ];
}
