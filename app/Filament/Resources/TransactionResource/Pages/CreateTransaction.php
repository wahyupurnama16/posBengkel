<?php
namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Sparepart;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Get the created transaction with its details
        $transaction = $this->record;
        // Loop through all transaction details
        foreach ($transaction->transaction_details as $detail) {
            // Find the sparepart
            $sparepart = Sparepart::find($detail->sparepart_id);

            if ($sparepart) {
                if ($sparepart->stock < $detail->quantity) {
                    Notification::make()
                        ->title('Insufficient Stock')
                        ->body("Requested quantity: {$detail->quantity}, Available: {$sparepart->stock}")
                        ->danger()
                        ->send();
                    $this->halt();
                }

                // Reduce the stock
                $sparepart->stock -= $detail->quantity;
                $sparepart->save();
            }
        }
    }

}
