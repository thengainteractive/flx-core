<?php

namespace Modules\Venue\Filament\Resources\VenueTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Modules\Venue\Filament\Resources\VenueTypeResource;

class ManageVenueTypes extends ManageRecords
{
    protected static string $resource = VenueTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
