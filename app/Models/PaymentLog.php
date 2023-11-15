<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function feePayment(){
        return $this->belongsTo(FeePayment::class, 'fee_payment_id', 'id');
    }
}
