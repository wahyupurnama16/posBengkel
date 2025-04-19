<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SparepartResource\Pages;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SparepartResource extends Resource
{
    protected static ?string $model = Sparepart::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\Textarea::make('description'),
                        Forms\Components\TextInput::make('price')->numeric()->required(),
                        Forms\Components\TextInput::make('stock')->numeric()->required(),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('price')->money('idr'),
                Tables\Columns\TextColumn::make('stock')
                    ->color(fn(Sparepart $record): string => $record->stock <= 1 ? 'danger' : 'success')
                    ->weight('bold')
                    ->formatStateUsing(function ($state, Sparepart $record) {
                        if ($record->stock <= 1) {
                            // Memunculkan notifikasi ketika rendering tabel
                            Notification::make()
                                ->title('Stok Menipis!')
                                ->body('Sparepart "' . $record->name . '" memiliki stok ' . $record->stock)
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('supplier.name'),
                Tables\Columns\ImageColumn::make('image')->circular(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSpareparts::route('/'),
            'create' => Pages\CreateSparepart::route('/create'),
            'edit'   => Pages\EditSparepart::route('/{record}/edit'),
        ];
    }
}
