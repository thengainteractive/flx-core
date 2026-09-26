<?php

namespace Modules\Venue\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Venue\Models\VenueType;
use Modules\Venue\Filament\Resources\VenueTypeResource\Pages;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class VenueTypeResource extends Resource
{
    protected static ?string $model = VenueType::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = 'Venue Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Venue Type Name')
                    ->required()
                    ->maxLength(255)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set, string $operation) {
                        if ($operation !== 'create' || blank($state)) return;
                        
                        $slug = \Illuminate\Support\Str::slug($state);
                        $originalSlug = $slug;
                        $count = 1;
                        
                        while (\Modules\Venue\Models\VenueType::where('slug', $slug)->exists()) {
                            $slug = "{$originalSlug}-" . $count++;
                        }
                        
                        $set('slug', $slug);
                    }),
                Forms\Components\TextInput::make('slug')
                    ->label('Web Address (URL Link)')
                    ->helperText('The unique web address for this venue type. You can customize this.')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
            'index' => Pages\ManageVenueTypes::route('/'),
        ];
    }
}
