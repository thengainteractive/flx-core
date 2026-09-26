<?php

namespace Modules\Venue\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Venue\Models\Venue;
use Modules\Venue\Filament\Resources\VenueResource\Pages;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string | \UnitEnum | null $navigationGroup = 'Venue Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make([
                    \Filament\Schemas\Components\Section::make('General Information')->components([
                        Forms\Components\TextInput::make('name')
                            ->label('Venue Name')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set, string $operation) {
                                if ($operation !== 'create' || blank($state)) return;
                                
                                $slug = \Illuminate\Support\Str::slug($state);
                                $originalSlug = $slug;
                                $count = 1;
                                
                                while (\Modules\Venue\Models\Venue::where('slug', $slug)->exists()) {
                                    $slug = "{$originalSlug}-" . $count++;
                                }
                                
                                $set('slug', $slug);
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label('Web Address (URL Link)')
                            ->helperText('The unique web address for this venue. You can customize this.')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('location_id')
                            ->relationship('location', 'city')
                            ->getOptionLabelFromRecordUsing(fn (\Modules\Venue\Models\Location $record) => $record->full_name)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('city')
                                    ->label('City')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set) {
                                        if (blank($state)) return;
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
                                    ->required()
                                    ->unique(\Modules\Venue\Models\Location::class, 'slug')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Select::make('venue_type_id')
                            ->relationship('venueType', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Venue Type Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set) {
                                        if (blank($state)) return;
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
                                    ->required()
                                    ->unique(\Modules\Venue\Models\VenueType::class, 'slug')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\RichEditor::make('description')
                            ->toolbarButtons([]) // Removes all formatting buttons
                            ->mutateDehydratedStateUsing(fn (?string $state) => $state ? strip_tags($state, ['p', 'br']) : null)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('tagline')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city_area')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('pricing_unit')
                            ->options([
                                'per_hour' => 'Per Hour',
                                'per_day' => 'Per Day',
                                'per_person' => 'Per Person',
                                'fixed' => 'Fixed Price',
                            ])
                            ->default('per_day'),
                    ])->columns(2),

                    \Filament\Schemas\Components\Section::make('Capacities & Areas')->components([
                        Forms\Components\TextInput::make('max_floating_capacity')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_seated_capacity')
                            ->numeric(),
                        Forms\Components\TextInput::make('rooms_available')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('total_area_sq_ft')
                            ->numeric(),
                        Forms\Components\TextInput::make('parking_spots')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                    \Filament\Schemas\Components\Section::make('Policies & Lists')->components([
                        Forms\Components\Select::make('catering_policy')
                            ->options([
                                'in_house' => 'In-House Only',
                                'outside_allowed' => 'Outside Allowed',
                                'none' => 'No Catering',
                            ]),
                        Forms\Components\Select::make('alcohol_policy')
                            ->options([
                                'allowed' => 'Allowed',
                                'not_allowed' => 'Not Allowed',
                                'license_required' => 'License Required',
                            ]),
                        Forms\Components\TagsInput::make('highlights')
                            ->suggestions(fn () => \Modules\Venue\Models\Venue::whereNotNull('highlights')->pluck('highlights')->flatten()->unique()->filter()->values()->toArray()),
                        Forms\Components\TagsInput::make('amenities')
                            ->suggestions(fn () => \Modules\Venue\Models\Venue::whereNotNull('amenities')->pluck('amenities')->flatten()->unique()->filter()->values()->toArray()),
                        Forms\Components\TagsInput::make('rules')
                            ->suggestions(fn () => \Modules\Venue\Models\Venue::whereNotNull('rules')->pluck('rules')->flatten()->unique()->filter()->values()->toArray())
                            ->columnSpanFull(),
                    ])->columns(2),
                ])->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make([
                    \Filament\Schemas\Components\Section::make('Status')->components([
                        Forms\Components\Toggle::make('verified')
                            ->default(false),
                    ]),
                    
                    \Filament\Schemas\Components\Section::make('Media')->components([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('gallery')
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->maxSize(10240) // 10MB limit to prevent PHP memory crashes
                            ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string {
                                return (string) \Illuminate\Support\Str::uuid() . '.webp';
                            })
                            ->mediaName(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string {
                                return (string) \Illuminate\Support\Str::uuid();
                            })
                            ->afterStateUpdated(function (?array $state, \Filament\Schemas\Components\Utilities\Set $set) {
                                if (blank($state)) return;
                                
                                $validFiles = [];
                                foreach ($state as $key => $file) {
                                    if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                        $path = $file->getRealPath();
                                        
                                        if (file_exists($path)) {
                                            try {
                                                \Spatie\Image\Image::load($path)
                                                    ->format('webp')
                                                    ->quality(75)
                                                    ->optimize()
                                                    ->save($path);
                                                $validFiles[$key] = $file;
                                            } catch (\Exception $e) {
                                                \Illuminate\Support\Facades\Log::error('Venue image conversion failed: ' . $e->getMessage());
                                                if (file_exists($path)) {
                                                    unlink($path);
                                                }
                                            }
                                        }
                                    } else {
                                        $validFiles[$key] = $file; // Keep existing non-temporary files
                                    }
                                }
                                
                                // Update state with only valid files
                                if (count($validFiles) !== count($state)) {
                                    $set('gallery', $validFiles);
                                }
                            }),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('gallery')
                    ->collection('gallery')
                    ->limit(1)
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.city')
                    ->label('City')
                    ->sortable(),
                Tables\Columns\TextColumn::make('venueType.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable()
                    ->description(fn (\Modules\Venue\Models\Venue $record): string => match($record->pricing_unit) {
                        'per_hour' => 'per hour',
                        'per_day' => 'per day',
                        'per_person' => 'per person',
                        'fixed' => 'fixed price',
                        default => '',
                    }),
                Tables\Columns\IconColumn::make('verified')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('location_id')
                    ->relationship('location', 'city')
                    ->getOptionLabelFromRecordUsing(fn (\Modules\Venue\Models\Location $record) => $record->full_name)
                    ->label('Location'),
                Tables\Filters\SelectFilter::make('venue_type_id')
                    ->relationship('venueType', 'name')
                    ->label('Venue Type'),
                Tables\Filters\TernaryFilter::make('verified'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // VenueSpacesRelationManager could be added here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
