<?php
namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\WorkService;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TransactionDetails extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = TransactionResource::class;
    protected static string $view     = 'filament.resources.transaction-resource.pages.transaction-details';

    public Transaction $record;

    public function mount(Transaction $record): void
    {
        $this->record = $record;
    }

    protected function getTableQuery(): Builder
    {
        return TransactionDetail::query()
            ->select(
                'sparepart_id',
                DB::raw('MIN(id) as id'), // Use the first ID for reference
                DB::raw('SUM(quantity) as quantity'),
                'price', // Assuming price is the same for identical spareparts
                DB::raw('SUM(quantity * price) as subtotal')
            )
            ->where('transaction_id', $this->record->id)
            ->groupBy('sparepart_id', 'price') // Group by both sparepart_id and price
            ->orderBy('id');                   // Optional: maintain original order
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

    public function getViewData(): array
    {
        $transactionId = $this->record->id;
        // Ambil data work services dari database
        $workServices = WorkService::where('transaction_id', $transactionId)
            ->get();
        // Mengembalikan data tambahan untuk view
        return [
            'workServices' => $workServices,
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->url(fn(TransactionDetail $record): string =>
                    route('filament.admin.resources.transaction-details.edit', ['record' => $record])),
            Tables\Actions\DeleteAction::make(),
        ];
    }

}
