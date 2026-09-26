<?php

namespace Modules\Invoicing\Filament\Resources\Invoices;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Invoicing\Filament\Resources\Invoices\Pages\CreateInvoice;
use Modules\Invoicing\Filament\Resources\Invoices\Pages\EditInvoice;
use Modules\Invoicing\Filament\Resources\Invoices\Pages\ListInvoices;
use Modules\Invoicing\Filament\Resources\Invoices\Schemas\InvoiceForm;
use Modules\Invoicing\Filament\Resources\Invoices\Tables\InvoicesTable;
use Modules\Invoicing\Models\Invoice;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Invoicing';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
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
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'edit' => EditInvoice::route('/{record}/edit'),
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
