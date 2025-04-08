<?php
namespace App\Filament\Resources\WorkServiceResource\Pages;

use App\Filament\Resources\WorkServiceResource;
use App\Models\Transaction;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkService extends CreateRecord
{
    protected static string $resource = WorkServiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? route('filament.admin.resources.transactions.index');
    }

    protected function afterCreate(): void
    {
        $data                      = $this->record;
        $transaction               = Transaction::find($data->transaction_id);
        $transaction->total_amount = $transaction->total_amount + (int) $data->price;
        $transaction->save();
    }

}
