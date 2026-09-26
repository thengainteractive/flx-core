<?php

namespace Modules\Page\Filament\Resources\Pages;

use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Page\Filament\Resources\Pages\Pages\CreatePage;
use Modules\Page\Filament\Resources\Pages\Pages\EditPage;
use Modules\Page\Filament\Resources\Pages\Pages\ListPages;
use Modules\Page\Filament\Resources\Pages\Schemas\PageForm;
use Modules\Page\Filament\Resources\Pages\Tables\PagesTable;
use Modules\Page\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
