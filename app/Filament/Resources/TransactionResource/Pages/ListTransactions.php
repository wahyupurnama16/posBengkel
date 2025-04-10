<?php
namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\CreateAction::make('wa')
                ->label('Whatsapp')
                ->url(fn() => route('admin.laporan'))
                ->color('success')
                ->icon('heroicon-o-document-text'),
        ];
    }

}
