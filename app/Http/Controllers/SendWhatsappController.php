<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WorkService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class SendWhatsappController extends Controller
{
    public function send($inv)
    {
        $transaksi     = Transaction::with(['customer', 'transaction_details'])->where('invoice', $inv)->first();
        $works         = WorkService::with(['transaction'])->where('transaction_id', $transaksi->id)->get();
        $token         = 'Ld6WkAE6yDfagi9fSbKR';
        $listSparepart = '';
        $listJasa      = '';
        if ($transaksi->transaction_details) {
            foreach ($transaksi->transaction_details as $detailTransaksi) {
                $listSparepart = $detailTransaksi->sparepart->name . " (" . $detailTransaksi->quantity . ") - " . "IDR " . number_format($detailTransaksi->price) . "\n";
            }
        }

        if ($works) {
            foreach ($works as $work) {
                $listJasa = $work->name . " - IDR " . number_format($work->price) . "\n";
            }
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => [
                "target"   => $transaksi->customer->phone,
                "message"  => "Halo " . $transaksi->customer->name . " , \nKonfirmasi transaksi Anda:  \n\nInvoice: " . $inv . "\nTanggal: " . date('d M Y', strtotime($transaksi->created_at)) . " \n\nSparepart: \n" . $listSparepart . "\n\nJasa: \n" . $listJasa . "\n\n*Total: IDR 174.246,00* \n\nTerima kasih atas pembelian Anda. Jika ada pertanyaan, silakan hubungi kami kembali.",
                "schedule" => 0,
                'typing'   => false,
            ],
            CURLOPT_HTTPHEADER     => [
                'Authorization: ' . $token,
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            Log::info($error_msg);
        }

        Log::info('text : berhasil terkirim to ' . $transaksi->customer->phone);
        Log::info($response);
        curl_close($curl);
        return $response;

    }
}
