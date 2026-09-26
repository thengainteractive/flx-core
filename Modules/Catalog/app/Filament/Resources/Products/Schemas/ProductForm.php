<?php

namespace Modules\Catalog\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('sku', strtoupper(\Illuminate\Support\Str::slug($state)))),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->unique('products', 'sku', ignoreRecord: true),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->required(),
                TextInput::make('tax_rate')
                    ->label('Tax Rate')
                    ->numeric()
                    ->default(0)
                    ->suffix('%')
                    ->datalist(function () {
                        if (class_exists(\Modules\Invoicing\Models\Tax::class)) {
                            return \Modules\Invoicing\Models\Tax::where('is_active', true)->pluck('rate')->toArray();
                        }
                        return [];
                    }),
                TextInput::make('stock')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('In Stock / Active')
                    ->default(true),
            ]);
    }
}
