<?php

namespace Modules\Blog\Filament\Resources\Categories;

use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Blog\Filament\Resources\Categories\Pages\CreateCategory;
use Modules\Blog\Filament\Resources\Categories\Pages\EditCategory;
use Modules\Blog\Filament\Resources\Categories\Pages\ListCategories;
use Modules\Blog\Filament\Resources\Categories\Schemas\CategoryForm;
use Modules\Blog\Filament\Resources\Categories\Tables\CategoriesTable;
use Modules\Blog\Models\Category;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Blog';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
