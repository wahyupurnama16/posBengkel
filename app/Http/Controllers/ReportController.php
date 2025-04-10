<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    public function index()
    {
        $transaksi = Transaction::all();
        $pdf       = Pdf::loadView('pdf.transaksi', $transaksi);
        return $pdf->stream();

    }
}
