<?php

namespace App\Models;

use App\Models\Sparepart;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id', 'sparepart_id', 'quantity', 'price', 'subtotal'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
