<?php
namespace App\Filament\Resources;

use App\Filament\Resources\TransactionDetailResource\Pages;
use App\Models\Sparepart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Request;

class TransactionDetailResource extends Resource
{
    protected static ?string $model = TransactionDetail::class;

    protected static ?string $navigationIcon        = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([

                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->default(fn() =>
                                Request::query('invoice')
                            )
                            ->disabled(),
                        Forms\Components\hidden::make('transaction_id')
                            ->default(function () {
                                $trasaction = Transaction::where('invoice', Request::query('invoice'))->first();
                                return $trasaction->id;
                            }),

                        Forms\Components\Select::make('sparepart_id')
                            ->relationship('sparepart', 'name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set) {
                                // Find the sparepart
                                if ($state === null) {
                                    return;
                                }

                                $sparepart = Sparepart::find($state);
                                if ($sparepart) {
                                    $price = $sparepart->price;
                                    $set('price', floatval($price));
                                    $set("quantity", 1);
                                    // Calculate subtotal based on current quantity (default to 1)
                                    $set('subtotal', floatval($price));
                                }
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, Set $set, Get $get) {
                                $sparepart = Sparepart::find($get('sparepart_id'));
                                $price     = $get('price');
                                $quantity  = intval($state);
                                if ($sparepart->stock >= $quantity) {
                                    if ($price && $quantity > 0) {
                                        $subtotal = floatval($price) * $quantity;
                                        $set('subtotal', $subtotal);
                                    }
                                } else {
                                    Notification::make()
                                        ->title('Insufficient Stock')
                                        ->body("Available stock: {$sparepart->stock}. You requested: {$quantity}")
                                        ->danger()
                                        ->send();

                                    // Reset values
                                    $set('quantity', 1);
                                    $set('subtotal', floatval($price));
                                }
                            }),
                        Forms\Components\TextInput::make('price')->numeric()->required()->readonly(),
                        Forms\Components\TextInput::make('subtotal')->numeric()->required()->readonly(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction.id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sparepart.name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('price')->money('idr'),
                Tables\Columns\TextColumn::make('subtotal')->money('idr'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_id')
                    ->relationship('transaction', 'id')
                    ->label('Transaction ID'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTransactionDetails::route('/'),
            'create' => Pages\CreateTransactionDetail::route('/create'),
            'edit'   => Pages\EditTransactionDetail::route('/{record}/edit'),
        ];
    }
}
