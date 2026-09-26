<?php

namespace Modules\Venue\Filament\Resources\VenueResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Venue\Filament\Resources\VenueResource;

class ListVenues extends ListRecords
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
