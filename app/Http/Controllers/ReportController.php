<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter filter langsung dari URL referer jika tersedia
        $referer       = $request->headers->get('referer');
        $refererParams = [];

        if ($referer) {
            $refererUrl = parse_url($referer);
            if (isset($refererUrl['query'])) {
                parse_str($refererUrl['query'], $refererParams);
            }
        }

// Gunakan parameter dari referer atau request langsung
        $params = ! empty($refererParams) ? $refererParams : $request->all();

// Extract filter values
        $customerId        = $params['tableFilters']['customer_id']['value'] ?? null;
        $statusTransaction = $params['tableFilters']['status_transaction']['value'] ?? null;
        $dateFrom          = $params['tableFilters']['transaction_date']['transaction_from'] ?? null;
        $dateUntil         = $params['tableFilters']['transaction_date']['transaction_until'] ?? null;

// Query logic sama seperti sebelumnya...
        $query = Transaction::query();

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($statusTransaction) {
            $query->where('status_transaction', $statusTransaction);
        }

        if ($dateFrom && $dateUntil) {
            $query->whereDate('transaction_date', '>=', $dateFrom)
                ->whereDate('transaction_date', '<=', $dateUntil);
        } else {
            $query->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year);
        }

        $transactions = $query->with(['customer', 'transaction_details.sparepart', 'work_services'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Hitung total nilai transaksi
        $totalAmount = $query->sum('total_amount');

        $totalSemua = $query->sum('total_amount');
        $pdf        = Pdf::loadView('pdf.transaksi', ['transactions' => $transactions, 'totalAmount' => $totalAmount, 'totalSemua' => $totalSemua, 'dateFrom' => $dateFrom, 'dateUntil' => $dateUntil])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-transaksi' . now() . '.pdf');
    }
}
