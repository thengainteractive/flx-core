<?php

namespace Modules\Invoicing\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                // LEFT COLUMN (Main Details & Line Items)
                \Filament\Schemas\Components\Group::make()->schema([
                    
                    Section::make('Invoice Details')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('invoice_number')->required()->unique(ignoreRecord: true),
                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'sent' => 'Sent',
                                    'partial' => 'Partially Paid',
                                    'paid' => 'Paid',
                                    'overdue' => 'Overdue',
                                    'cancelled' => 'Cancelled',
                                ])->default('draft')->required(),
                            DatePicker::make('issue_date')->required()->default(now()),
                            DatePicker::make('due_date'),
                        ]),
                    ]),

                    Section::make('Customer Information')->schema([
                        Select::make('contact_id')
                            ->label('Select Existing Client (Optional)')
                            ->options(function () {
                                return class_exists(\Modules\Contacts\Models\Contact::class) 
                                    ? \Modules\Contacts\Models\Contact::pluck('name', 'id') 
                                    : [];
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (!$state) return;
                                if (class_exists(\Modules\Contacts\Models\Contact::class)) {
                                    $contact = \Modules\Contacts\Models\Contact::find($state);
                                    if ($contact) {
                                        $set('customer_name', $contact->name);
                                        $set('customer_email', $contact->email);
                                        $set('customer_address', $contact->billing_address);
                                    }
                                }
                            }),
                        Grid::make(2)->schema([
                            TextInput::make('customer_name')->required(),
                            TextInput::make('customer_email')->email(),
                        ]),
                        Textarea::make('customer_address')->rows(2),
                    ]),

                    Section::make('Line Items')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($get, $set) {
                                self::updateTotals($get, $set);
                            })
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(function () {
                                        return class_exists(\Modules\Catalog\Models\Product::class)
                                            ? \Modules\Catalog\Models\Product::pluck('name', 'id')
                                            : [];
                                    })
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if (!$state) return;
                                        if (class_exists(\Modules\Catalog\Models\Product::class)) {
                                            $product = \Modules\Catalog\Models\Product::find($state);
                                            if ($product) {
                                                $set('name', $product->name);
                                                $set('unit_price', $product->price);
                                                $set('tax_rate', $product->tax_rate);
                                                self::calculateLineItem($get, $set);
                                            }
                                        }
                                    })->columnSpan(3),
                                    
                                TextInput::make('name')->required()->columnSpan(4),
                                
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($get, $set) => self::calculateLineItem($get, $set))
                                    ->columnSpan(2),
                                    
                                TextInput::make('unit_price')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($get, $set) => self::calculateLineItem($get, $set))
                                    ->columnSpan(3),
                                    
                                Select::make('tax_rate')
                                    ->options(function () {
                                        return \Modules\Invoicing\Models\Tax::where('is_active', true)->pluck('name', 'rate');
                                    })
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($get, $set) => self::calculateLineItem($get, $set))
                                    ->columnSpan(4),

                                TextInput::make('total')
                                    ->numeric()
                                    ->readOnly()
                                    ->columnSpan(4),
                                    
                                TextInput::make('tax_amount')->hidden(),
                                
                            ])->columns(12)->columnSpanFull()
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data) {
                                return self::enforceCalculations($data);
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data) {
                                return self::enforceCalculations($data);
                            }),
                    ]),
                ])->columnSpan(['lg' => 2]),
                
                // RIGHT COLUMN (Totals)
                \Filament\Schemas\Components\Group::make()->schema([
                    Section::make('Summary')->schema([
                        TextInput::make('subtotal')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                        TextInput::make('tax_total')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                        TextInput::make('total')
                            ->label('Grand Total')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                        TextInput::make('amount_paid')
                            ->numeric()
                            ->default(0),
                    ]),
                    
                    Section::make('Additional')->schema([
                        Textarea::make('notes')->rows(4),
                    ]),
                ])->columnSpan(['lg' => 1]),
                
        ]);
    }

    public static function calculateLineItem($get, $set): void
    {
        $quantity = floatval($get('quantity') ?? 0);
        $unitPrice = floatval($get('unit_price') ?? 0);
        $taxRate = floatval($get('tax_rate') ?? 0);
        
        $subtotal = $quantity * $unitPrice;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;
        
        $set('tax_amount', round($taxAmount, 2));
        $set('total', round($total, 2));
    }

    public static function updateTotals($get, $set): void
    {
        // $get('../../') pulls data from the root form level when called from within the repeater
        // Actually, Filament passes the root $get to the repeater's ->afterStateUpdated().
        $items = $get('items') ?? [];
        $subtotal = 0;
        $taxTotal = 0;
        
        foreach ($items as $item) {
            $qty = floatval($item['quantity'] ?? 0);
            $price = floatval($item['unit_price'] ?? 0);
            $taxRate = floatval($item['tax_rate'] ?? 0);
            
            $itemSub = $qty * $price;
            $itemTax = $itemSub * ($taxRate / 100);
            
            $subtotal += $itemSub;
            $taxTotal += $itemTax;
        }
        
        $total = $subtotal + $taxTotal;
        
        $set('subtotal', round($subtotal, 2));
        $set('tax_total', round($taxTotal, 2));
        $set('total', round($total, 2));
    }

    public static function enforceCalculations(array $data): array
    {
        $qty = floatval($data['quantity'] ?? 0);
        $price = floatval($data['unit_price'] ?? 0);
        $taxRate = floatval($data['tax_rate'] ?? 0);
        $subtotal = $qty * $price;
        $data['tax_amount'] = $subtotal * ($taxRate / 100);
        $data['total'] = $subtotal + $data['tax_amount'];
        return $data;
    }
}
