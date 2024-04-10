<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranscriptRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function feepayment(){
        return $this->hasOne(FeePayment::class, 'request_id', 'id');
    }

    public function details(){
        return $this->hasOne(TranscriptDetail::class, 'request_id', 'id');
    }

    public function profile(){
        return $this->belongsTo(UserProfile::class, 'user_id','user_id');
    }

    public function feeConfig(){
        return $this->belongsTo(FeeConfig::class, 'fconfig','id');
    }

    public function type(){
        return $this->belongsTo(TranscriptType::class, 't_type','id');
    }
}
