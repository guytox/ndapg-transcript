<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePaymentItem extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function feePayment(){

        return $this->belongsTo(FeePayment::class, 'fee_payment_id' , 'id');
    }

    public function feeItem(){

        return $this->belongsTo(FeeItem::class,  'fee_item_id', 'id');
    }




}
