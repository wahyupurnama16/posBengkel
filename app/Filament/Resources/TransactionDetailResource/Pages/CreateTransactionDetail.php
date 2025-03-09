<?php

namespace App\Filament\Resources\TransactionDetailResource\Pages;

use App\Filament\Resources\TransactionDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionDetail extends CreateRecord
{
    protected static string $resource = TransactionDetailResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set transaction_id from request query parameter if it exists
        $data['transaction_id'] = request()->query('transaction_id', $data['transaction_id'] ?? null);
        return $data;
    }
    
   
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
