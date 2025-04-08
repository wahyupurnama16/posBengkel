<?php
namespace App\Filament\Resources\TransactionDetailResource\Pages;

use App\Filament\Resources\TransactionDetailResource;
use App\Models\Sparepart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionDetail extends CreateRecord
{
    protected static string $resource = TransactionDetailResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        // Check if we have enough stock
        $sparepart = Sparepart::find($this->data['sparepart_id']);
        $quantity  = $this->data['quantity'];

        if ($sparepart->stock < $quantity) {
            Notification::make()
                ->title('Insufficient Stock')
                ->body("Requested quantity: {$quantity}, Available: {$sparepart->stock}")
                ->danger()
                ->send();
            $this->halt();
        }

        // Calculate subtotal
        $this->data['subtotal'] = $this->data['price'] * $quantity;
    }

    protected function afterCreate(): void
    {
        // Deduct stock from inventory
        $sparepart = Sparepart::find($this->record->sparepart_id);

        $sparepart->decrement('stock', $this->record->quantity);

        // Update transaction total
        $this->recalculateTransactionTotal($this->record->transaction_id);

        // Show success notification
        Notification::make()
            ->title('Transaction detail created')
            ->body('Stock has been deducted from inventory')
            ->success()
            ->send();

        // parent::afterCreate();

        $this->getRedirectUrl();
    }

    /**
     * Recalculate the total amount for a transaction based on its details
     */
    protected function recalculateTransactionTotal(int $transactionId): void
    {
        // Get all transaction details for this transaction
        $details = TransactionDetail::where('transaction_id', $transactionId)->get();

        // Calculate the new total amount
        $newTotal = $details->sum('subtotal');

        // Update the transaction's total_amount
        Transaction::where('id', $transactionId)
            ->update(['total_amount' => $newTotal]);
    }
}
