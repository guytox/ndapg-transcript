<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentConfiguration, App\Models\PaymentLog, App\Models\User;

class FeePayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function configuration()
    {
        return $this->belongsTo(PaymentConfiguration::class, 'payment_config_id', 'id');
    }

    public function config()
    {
        return $this->belongsTo(FeeConfig::class, 'payment_config_id', 'id');
    }

    public function paymentLogs()
    {
        return $this->hasMany(PaymentLog::class, 'fee_payment_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(FeePaymentItem::class, 'fee_payment_id', 'id');
    }

    public function credoRequests(){
        return $this->hasMany(CredoRequest::class, 'fee_payment_id', 'id');
    }

    public function transcriptRequest(){
        return $this->hasOne(TranscriptRequest::class, 'id','request_id');
    }
}
