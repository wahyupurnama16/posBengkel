<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function calculateTotalAmount(): void
    {
        $total = 0;
        $data = $this->form->getState();
        
        if (isset($data['transaction_details']) && is_array($data['transaction_details'])) {
            foreach ($data['transaction_details'] as $item) {
                $total += $item['subtotal'] ?? 0;
            }
        }
        
        $this->form->fill(['total_amount' => $total]);
    }

    // Tambahkan juga method mount untuk pertama kali form dimuat
    protected function afterMount(): void
    {
        $this->calculateTotalAmount();
    }
}
