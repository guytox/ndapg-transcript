<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePaymentItem extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function feePayment(){

        return $this->belongsTo(FeePayment::class, 'id' , 'fee_payment_id');
    }




}
