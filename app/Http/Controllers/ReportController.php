<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    public function index()
    {
        $transaksi  = Transaction::all();
        $totalSemua = Transaction::sum('total_amount');
        $pdf        = Pdf::loadView('pdf.transaksi', ['transaksi' => $transaksi, 'totalSemua' => $totalSemua])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi' . now() . '.pdf');

    }
}
