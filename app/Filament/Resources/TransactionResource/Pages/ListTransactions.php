<?php
namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    // Property untuk tracking filter saat ini
    public $summaryData = [
        'totalAmount' => 0,
        'totalCount'  => 0,
    ];

    // Tambahkan property untuk menyimpan status filter
    public $activeFilters = [];

    public function mount(): void
    {
        parent::mount();
        $this->updatedTableFilters();
    }

    // Metode untuk memperbarui filter aktif
    public function updateActiveFilters(): void
    {
        $this->activeFilters = [
            'customer_id'        => request('tableFilters.customer_id.value') ?? null,
            'status_transaction' => request('tableFilters.status_transaction.value') ?? null,
            'transaction_from'   => request('tableFilters.transaction_date.transaction_from') ?? null,
            'transaction_until'  => request('tableFilters.transaction_date.transaction_until') ?? null,
        ];
    }

    // Hook yang dipanggil ketika filter berubah
    public function updatedTableFilters(): void
    {
        $this->updateActiveFilters();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            // Action PDF yang selalu menggunakan filter terbaru
            Actions\Action::make('laporan_pdf')
                ->label('Laporan PDF')
                ->url(function () {
                    return route('admin.laporan');
                })
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->extraAttributes([
                    'id'      => 'laporan-pdf-button',
                    'onclick' => "
                    event.preventDefault();
                    const currentUrl = new URL(window.location.href);
                    const baseReportUrl = '" . route('admin.laporan') . "';
                    const reportUrl = baseReportUrl + currentUrl.search;
                    window.open(reportUrl, '_blank');
                ",
                ])
                ->openUrlInNewTab(),
        ];
    }

}
