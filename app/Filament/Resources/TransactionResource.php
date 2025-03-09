<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\Sparepart;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\Pages\EditTransaction;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Filament\Resources\TransactionResource\Pages\CreateTransaction;
use App\Filament\Resources\TransactionResource\Pages\TransactionDetails;


class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                         TextInput::make('invoice')
                            ->readonly()
                            ->required()
                            ->default(function() {
                                $dateCode = now()->format('Ymd');
                                $latestTransaction = Transaction::where('invoice', 'like', "INV-{$dateCode}%")
                                    ->orderBy('id', 'desc')
                                    ->first();
                                if (!$latestTransaction) {
                                    $nextNumber = '0001';
                                } else {
                                    $lastNumber = substr($latestTransaction->invoice, -4);
                                    $nextNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
                                }
                                
                                // Gabungkan semua menjadi format INV-YYYYMMDD-0001
                                return "INV-{$dateCode}-{$nextNumber}";
                            }),
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->options(Customer::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')->email(),
                                Forms\Components\TextInput::make('address')
                                    ->maxLength(255),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Create customer')
                                    ->modalButton('Create customer')
                                    ->modalWidth('md');
                            })
                             ->createOptionUsing(function (array $data, Forms\Components\Select $component) {
                                $customer = Customer::create($data);
                                
                                // This forces the component to re-evaluate its options
                                $component->state($customer->id);
                                
                                // Return the customer ID to set it as the selected value
                                return $customer->id;
                            }),

                        Forms\Components\DatePicker::make('transaction_date')
                            ->required()
                            ->default(now()),
                        
                        Repeater::make('transaction_details')
                            ->relationship()
                            ->schema([
                                Select::make('sparepart_id')
                                    ->label('Sparepart')
                                    ->options(Sparepart::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        // Find the sparepart
                                        if ($state === null) return;
                                        $sparepart = Sparepart::find($state);
                                        if ($sparepart) {
                                            $price = $sparepart->price;
                                            $set('price', floatval($price));
                                            $set("quantity", 1);
                                            // Calculate subtotal based on current quantity (default to 1)
                                            $set('subtotal', floatval($price));
                                        }
                                    }),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Set $set, Get $get) {
                                        $sparepart = Sparepart::find($get('sparepart_id'));
                                        $price = $get('price');
                                        $quantity = intval($state);
                                        if($sparepart->stock >= $quantity){
                                            if ($price && $quantity > 0) {
                                                $subtotal = floatval($price) * $quantity;
                                                $set('subtotal', $subtotal);
                                            }
                                        }else{
                                            Notification::make()
                                                ->title('Insufficient Stock')
                                                ->body("Available stock: {$sparepart->stock}. You requested: {$quantity}")
                                                ->danger()
                                                ->send();
                                                
                                            // Reset values
                                            $set('quantity',  1);
                                            $set('subtotal', floatval($price));
                                        }
                                    }),

                                TextInput::make('price')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                // Calculate total directly from all transaction details
                                $details = $get('transaction_details');
                                if (is_array($details)) {
                                    $total = 0;
                                    foreach ($details as $detail) {
                                        if($detail['sparepart_id'] !== null){
                                            $sparepart = Sparepart::find($detail['sparepart_id']);

                                             if($sparepart->stock >= $detail['quantity']){
                                                $total += (int) $sparepart->price * $detail['quantity'];
                                             }else{
                                                 Notification::make()
                                                ->title('Insufficient Stock')
                                                ->body("Available stock: {$sparepart->stock}. You requested: {$detail['quantity']}")
                                                ->danger()
                                                ->send();
                                                
                                                // Reset values
                                                $set('quantity',  1);
                                                $set('subtotal', floatval($sparepart->price));
                                                $total += (int) $sparepart->price;
                                             }
                                           
                                        }
                                    }
                                    $set('total_amount', $total);
                                }
                            }),

                        TextInput::make('total_amount')
                            ->numeric()
                            ->readonly()
                            ->required(),
                    ])
            // Tambahkan ini untuk memaksa form melakukan perhitungan total setiap kali data berubah
            ->live(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('customer.name'),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                  Action::make('details_transaction')
                    ->label('Details Transaction')
                    ->url(fn (Transaction $record): string => 
                        route('filament.admin.resources.transactions.details', ['record' => $record]))
                    ->color('success')
                    ->icon('heroicon-o-document-text'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
             'details' => Pages\TransactionDetails::route('/{record}/details'),
        ];
    }
}