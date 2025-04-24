<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Laporan Transaksi</title>
  <style>
    table thead tr td {
      text-align: center
    }
  </style>
</head>

<body>

  <table border="0" width="100%">
    <tr>
      <td width="30%">BENGKEL VESPA LIFE</td>
      <td width="70%" align="right">LAPORAN PENJUALAN - {{ date('d M Y', strtotime($dateFrom)) .' s/d '. date('d M Y',
        strtotime($dateUntil)) }}
      </td>
    </tr>
  </table>

  <br>
  <br>

  <table border="1" style="border-collapse: collapse; width:100%">

    <thead>
      <tr>
        <td style="width:">No</td>
        <td style="width:">Invoice</td>
        <td>Customer</td>
        <td style="width:">Sparepart</td>
        <td style="width:">Jasa</td>
        <td>Total</td>
      </tr>
    </thead>
    <tbody>
      @php
      $i=1;
      @endphp
      @foreach ($transactions as $tr)
      <tr>
        <td style="text-align: center">{{ $i++ }}</td>
        <td style="text-align: center">{{ $tr->invoice }}</td>
        <td style="text-align: center">{{ $tr->customer->name }}</td>
        <td>
          <ol>
            @foreach ($tr->transaction_details as $trd)
            <li>{{ $trd->sparepart->name }} ({{ $trd->quantity }}) - Rp {{
              number_format($trd->sparepart->price) }}</li>
            @endforeach
          </ol>
        </td>
        <td>
          <ol>
            @foreach ($tr->work_services as $twr)
            <li>{{ $twr->name }} - Rp {{ number_format($twr->price) }}</li>
            @endforeach
          </ol>
        </td>
        <td style="text-align: center">Rp {{ number_format($tr->total_amount) }}</td>
      </tr>
      @endforeach
      <tr>
        <td colspan="5" style="text-align: right">Total:</td>
        <td>Rp {{ number_format($totalSemua) }}</td>
      </tr>

    </tbody>
  </table>

  <br>
  <br>
  <br>
  <table border="0" style="border-collapse: collapse; width:100%; text-align:right;">
    <tr>
      <td>Pemilik &nbsp; &nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td>
        <br>
        <br>
        <br>
      </td>
    </tr>
    <tr>
      <td>Vespa Life &nbsp; </td>
    </tr>
  </table>
</body>

</html>