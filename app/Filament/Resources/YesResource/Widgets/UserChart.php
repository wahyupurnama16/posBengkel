<?php

namespace App\Filament\Resources\YesResource\Widgets;

use Carbon\Carbon;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Card;

class UserChart extends ChartWidget
{

   protected static ?string $heading = 'Transaksi Bulanan Berdasarkan Status';
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getData(): array
    {
        // Mendapatkan data transaksi per bulan dan status
        $transactions = DB::table('transactions')
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('YEAR(transaction_date) as year'),
                'status_transaction',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->whereYear('transaction_date', '=', date('Y')) // Filter untuk tahun ini
            ->groupBy('year', 'month', 'status_transaction')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Mengorganisir data untuk chart
        $months = [];
        $pendingData = [];
        $successData = [];
        $cancelData = [];
        
        // Inisialisasi array untuk 12 bulan
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->format('M');
            $months[] = $monthName;
            $pendingData[$i] = 0;
            $successData[$i] = 0;
            $cancelData[$i] = 0;
        }
        
        // Mengisi data berdasarkan hasil query
        foreach ($transactions as $transaction) {
            $month = $transaction->month;
            
            if ($transaction->status_transaction === 'pending') {
                $pendingData[$month] = $transaction->total_transactions;
            } elseif ($transaction->status_transaction === 'success') {
                $successData[$month] = $transaction->total_transactions;
            } elseif ($transaction->status_transaction === 'cancel') {
                $cancelData[$month] = $transaction->total_transactions;
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Pending',
                    'data' => array_values($pendingData),
                    'backgroundColor' => '#FFA500', // Orange
                    'borderColor' => '#FFA500',
                ],
                [
                    'label' => 'Success',
                    'data' => array_values($successData),
                    'backgroundColor' => '#4BC0C0', // Teal
                    'borderColor' => '#4BC0C0',
                ],
                [
                    'label' => 'Cancel',
                    'data' => array_values($cancelData),
                    'backgroundColor' => '#FF6384', // Red
                    'borderColor' => '#FF6384',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa diganti dengan 'line' jika ingin tampilan grafik garis
    }
}
