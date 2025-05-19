<?php
namespace App\Models;

use App\Models\Customer;
use App\Models\TransactionDetail;
use App\Models\WorkService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'vehicle_number', 'transaction_date', 'total_amount', 'invoice', 'status_transaction'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction_details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function work_services(): HasMany
    {
        return $this->hasMany(WorkService::class);
    }
}
