<?php
namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    // Property to track the current filter state
    public $summaryData = [
        'totalAmount' => 0,
        'totalCount'  => 0,
    ];

    // Hook this method to mount lifecycle
    public function mount(): void
    {
        parent::mount();
        $this->calculateSummary();
    }

    // Calculate summaries based on current filters
    public function calculateSummary(): void
    {
        $query = Transaction::query();
        // Apply the same filters that are active in the table
        if (request('tableFilters.customer_id')) {
            $query->where('customer_id', request('tableFilters.customer_id'));
        }

        if (request('tableFilters.status_transaction')) {
            $query->where('status_transaction', request('tableFilters.status_transaction'));
        }

        if (request('tableFilters.transaction_date.transaction_from')) {
            $query->whereDate('transaction_date', '>=', request('tableFilters.transaction_date.transaction_from'));
        }

        if (request('tableFilters.transaction_date.transaction_until')) {
            $query->whereDate('transaction_date', '<=', request('tableFilters.transaction_date.transaction_until'));
        }
        $this->summaryData['totalAmount'] = $query->sum('total_amount') > 0 ? $query->sum('total_amount') : 0;
        $this->summaryData['totalCount']  = $query->count() > 0 ? $query->count() : 0;

    }

    // This hook will be called when filters change
    public function updatedTableFilters(): void
    {
        $this->calculateSummary();
    }

    // Override header actions to include our dynamic summary
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Actions\Action::make('transaction_summary')
            //     ->label(fn() => "Total Transaksi: IDR " .
            //         number_format($this->summaryData['totalAmount'], 0, ',', '.') .
            //         " ({$this->summaryData['totalCount']} transaksi)")
            //     ->color('warning')
            //     ->icon('heroicon-o-calculator')
            //     ->disabled(),
            Actions\CreateAction::make('wa')
                ->label('Laporan PDF')
                ->url(fn() => route('admin.laporan'))
                ->color('success')
                ->icon('heroicon-o-document-text'),
        ];
    }

}
