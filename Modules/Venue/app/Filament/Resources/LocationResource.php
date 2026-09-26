<?php

namespace Modules\Venue\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Venue\Models\Location;
use Modules\Venue\Filament\Resources\LocationResource\Pages;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';
    protected static string | \UnitEnum | null $navigationGroup = 'Venue Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('city')
                    ->label('City')
                    ->required()
                    ->maxLength(255)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set, string $operation) {
                        if ($operation !== 'create' || blank($state)) return;
                        
                        $slug = \Illuminate\Support\Str::slug($state);
                        $originalSlug = $slug;
                        $count = 1;
                        
                        while (\Modules\Venue\Models\Location::where('slug', $slug)->exists()) {
                            $slug = "{$originalSlug}-" . $count++;
                        }
                        
                        $set('slug', $slug);
                    }),
                Forms\Components\TextInput::make('district')
                    ->label('District')
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->label('State')
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label('Web Address (URL Link)')
                    ->helperText('The unique web address for this location. You can customize this.')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLocations::route('/'),
        ];
    }
}
