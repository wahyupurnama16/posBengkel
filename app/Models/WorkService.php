<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkService extends Model
{
    protected $fillable = ['transaction_id', 'name', 'price'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

}
