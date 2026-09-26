<?php

namespace Modules\Contacts\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('company_name'),
                Textarea::make('billing_address')
                    ->columnSpanFull(),
                Textarea::make('shipping_address')
                    ->columnSpanFull(),
                TextInput::make('tax_number'),
            ]);
    }
}
