<?php

namespace Modules\Page\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique('pages', 'slug', ignoreRecord: true),
                Select::make('layout')
                    ->options([
                        'default' => 'Default Layout',
                        'full-width' => 'Full Width',
                        'landing' => 'Landing Page (No header/footer)',
                    ])
                    ->default('default')
                    ->required(),
                Textarea::make('content')
                    ->rows(8)
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Published')
                    ->default(true),
            ]);
    }
}
