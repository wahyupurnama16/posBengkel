<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionDetails extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static string $resource = TransactionResource::class;
    protected static string $view = 'filament.resources.transaction-resource.pages.transaction-details';
    
    public Transaction $record;
    
    public function mount(Transaction $record): void
    {
        $this->record = $record;
    }
    
    protected function getTableQuery(): Builder
    {
        return TransactionDetail::query()->where('transaction_id', $this->record->id);
    }
    
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('sparepart.name')
                ->label('Sparepart'),
            Tables\Columns\TextColumn::make('quantity')
                ->label('Qty'),
            Tables\Columns\TextColumn::make('price')
                ->money('IDR')
                ->label('Price'),
            Tables\Columns\TextColumn::make('subtotal')
                ->money('IDR'),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->url(fn (TransactionDetail $record): string => 
                    route('filament.admin.resources.transaction-details.edit', ['record' => $record])),
            Tables\Actions\DeleteAction::make(),
        ];
    }
}