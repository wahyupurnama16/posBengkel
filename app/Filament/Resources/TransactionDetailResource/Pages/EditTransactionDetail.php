<?php
namespace App\Filament\Resources\TransactionDetailResource\Pages;

use App\Filament\Resources\TransactionDetailResource;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransactionDetail extends EditRecord
{
    protected static string $resource = TransactionDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    // Recalculate total amount after deletion
                    $this->recalculateTransactionTotal($this->record->transaction_id);
                }),
        ];
    }

    protected function afterSave(): void
    {
        // Recalculate total amount after saving changes
        $this->recalculateTransactionTotal($this->record->transaction_id);
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

        // If there are no details, set total to 0
        if ($details->isEmpty()) {
            $newTotal = 0;
        }

        // Update the transaction's total_amount
        Transaction::where('id', $transactionId)
            ->update(['total_amount' => $newTotal]);
    }

    /**
     * Override the beforeSave method to ensure the subtotal is calculated correctly
     */
    protected function beforeSave(): void
    {
        // Calculate the subtotal based on quantity and price
        $quantity               = $this->data['quantity'];
        $price                  = $this->data['price'];
        $this->data['subtotal'] = $quantity * $price;
    }

    /**
     * Modify the form to include a hook for recalculating the subtotal
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pass data to the form unmodified
        return $data;
    }
}
