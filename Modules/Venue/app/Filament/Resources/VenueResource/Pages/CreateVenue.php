<?php

namespace Modules\Venue\Filament\Resources\VenueResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Venue\Filament\Resources\VenueResource;

class CreateVenue extends CreateRecord
{
    protected static string $resource = VenueResource::class;
}
