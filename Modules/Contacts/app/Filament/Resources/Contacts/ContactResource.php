<?php

namespace Modules\Contacts\Filament\Resources\Contacts;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Contacts\Filament\Resources\Contacts\Pages\CreateContact;
use Modules\Contacts\Filament\Resources\Contacts\Pages\EditContact;
use Modules\Contacts\Filament\Resources\Contacts\Pages\ListContacts;
use Modules\Contacts\Filament\Resources\Contacts\Schemas\ContactForm;
use Modules\Contacts\Filament\Resources\Contacts\Tables\ContactsTable;
use Modules\Contacts\Models\Contact;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static \UnitEnum|string|null $navigationGroup = 'CRM';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'create' => CreateContact::route('/create'),
            'edit' => EditContact::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
