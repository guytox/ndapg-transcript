<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeConfig extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function feeTemplate(){
        return $this->hasOne(FeeTemplate::class,'id','fee_template_id');
    }

    public function feeCategory(){

        return $this->hasOne(FeeCategory::class, 'id', 'fee_category_id');
    }

}
