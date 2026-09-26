<?php

namespace Modules\Venue\Filament\Resources\VenueResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Venue\Filament\Resources\VenueResource;

class EditVenue extends EditRecord
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
