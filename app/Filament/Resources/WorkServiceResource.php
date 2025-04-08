<?php
namespace App\Filament\Resources;

use App\Filament\Resources\WorkServiceResource\Pages;
use App\Models\Transaction;
use App\Models\WorkService;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Request;

class WorkServiceResource extends Resource
{
    protected static ?string $model = WorkService::class;

    protected static ?string $navigationIcon        = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([

                        Select::make('transaction_id')
                            ->relationship('transaction', 'invoice')
                            ->required()
                            ->default(function () {
                                $trasaction = Transaction::where('invoice', Request::query('invoice'))->first();
                                return $trasaction->id;
                            }),

                        TextInput::make('name')
                            ->string()
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction.invoice')
                    ->label('Invoice')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_name')
                    ->label('Jasa'),
                Tables\Columns\TextColumn::make('price')
                    ->money('idr'),
                Tables\Columns\TextColumn::make('subtotal')
                    ->money('idr'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_id')
                    ->relationship('transaction', 'invoice')
                    ->label('Invoice'),
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
            'index'  => Pages\ListWorkServices::route('/'),
            'create' => Pages\CreateWorkService::route('/create'),
            'edit'   => Pages\EditWorkService::route('/{record}/edit'),
        ];
    }
}
