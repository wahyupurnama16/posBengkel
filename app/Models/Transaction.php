<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'transaction_date', 'total_amount','invoice'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction_details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
