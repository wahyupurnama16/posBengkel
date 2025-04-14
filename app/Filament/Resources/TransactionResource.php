<?php
namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Customer;
use App\Models\Sparepart;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model           = Transaction::class;
    protected static ?string $navigationIcon  = 'heroicon-o-shopping-cart';
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
                            ->default(function () {
                                $dateCode          = now()->format('Ymd');
                                $latestTransaction = Transaction::where('invoice', 'like', "INV-{$dateCode}%")
                                    ->orderBy('id', 'desc')
                                    ->first();
                                if (! $latestTransaction) {
                                    $nextNumber = '0001';
                                } else {
                                    $lastNumber = substr($latestTransaction->invoice, -4);
                                    $nextNumber = str_pad((int) $lastNumber + 1, 4, '0', STR_PAD_LEFT);
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

                        Forms\Components\Select::make('status_transaction')
                            ->label('Status')
                            ->options(['pending' => 'Pending', 'finish' => 'Finish', 'cancel' => 'Cancel'])
                            ->required(),

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

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
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

                                TextInput::make('price')
                                    ->numeric()
                                    ->required()
                                    ->readonly(),

                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->required()
                                    ->readonly(),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                // Calculate total directly from all transaction details
                                $details      = $get('transaction_details');
                                $WorkServices = $get('work_services');
                                if (is_array($details)) {
                                    $total        = 0;
                                    $totalService = 0;
                                    foreach ($details as $detail) {
                                        if ($detail['sparepart_id'] !== null) {
                                            $sparepart = Sparepart::find($detail['sparepart_id']);

                                            if ($sparepart->stock >= $detail['quantity']) {
                                                $total += (int) $sparepart->price * $detail['quantity'];
                                            } else {
                                                Notification::make()
                                                    ->title('Insufficient Stock')
                                                    ->body("Available stock: {$sparepart->stock}. You requested: {$detail['quantity']}")
                                                    ->danger()
                                                    ->send();

                                                // Reset values
                                                $set('quantity', 1);
                                                $set('subtotal', floatval($sparepart->price));
                                                $total += (int) $sparepart->price;
                                            }

                                        }
                                    }

                                    if (is_array($WorkServices)) {
                                        foreach ($WorkServices as $service) {
                                            if ($service['name'] !== null) {
                                                $totalService += (int) $service['price'];
                                            }
                                        }
                                    }

                                    $set('total_amount', $total + $totalService);
                                }
                            }),

                        Repeater::make('work_services')
                            ->relationship('work_services')
                            ->label('Jasa')
                            ->schema([
                                TextInput::make('name')
                                    ->string()
                                    ->required(),

                                TextInput::make('price')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $details         = $get('transaction_details');
                                $total_sparepart = 0;
                                if (is_array($details)) {
                                    foreach ($details as $detail) {
                                        if ($detail['sparepart_id'] !== null) {
                                            $sparepart = Sparepart::find($detail['sparepart_id']);

                                            if ($sparepart->stock >= $detail['quantity']) {
                                                $total_sparepart += (int) $sparepart->price * $detail['quantity'];
                                            } else {
                                                Notification::make()
                                                    ->title('Insufficient Stock')
                                                    ->body("Available stock: {$sparepart->stock}. You requested: {$detail['quantity']}")
                                                    ->danger()
                                                    ->send();

                                                // Reset values
                                                $set('quantity', 1);
                                                $set('subtotal', floatval($sparepart->price));
                                                $total_sparepart += (int) $sparepart->price;
                                            }

                                        }
                                    }
                                }

                                $datas = $get('work_services');
                                if (is_array($datas)) {
                                    $total = 0;
                                    foreach ($datas as $data) {
                                        $price = floatval($data['price']);
                                        $total += (int) $price;
                                    }
                                    $set('total_amount', $total_sparepart + $total);

                                }

                            }),

                        TextInput::make('total_amount')
                            ->numeric()
                            ->readonly()
                            ->required(),
                    ])
                    ->live(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name'),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status_transaction')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending'                         => 'warning',
                        'finish'                          => 'success',
                        'cancel'                          => 'danger',
                        default                           => 'gray',
                    }),
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
                    ->url(fn(Transaction $record): string =>
                        route('filament.admin.resources.transactions.details', ['record' => $record]))
                    ->color('success')
                    ->icon('heroicon-o-document-text'),
                Action::make('wa')
                    ->label('Whatsapp')
                    ->url(fn(Transaction $record): string =>
                        route('sendWa', ['record' => $record->invoice]))
                    ->color('success')
                    ->icon('heroicon-o-document-text'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListTransactions::route('/'),
            'create'  => Pages\CreateTransaction::route('/create'),
            'edit'    => Pages\EditTransaction::route('/{record}/edit'),
            'details' => Pages\TransactionDetails::route('/{record}/details'),
        ];
    }

}
