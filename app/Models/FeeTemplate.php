<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function FeeType() {

        return $this->hasOne(FeeType::class);
    }

    public function feeTemplateItems(){

        return $this->hasMany(FeeTemplateItem::class);
    }

    public function feeConfig(){
        
        return $this->belongsTo(FeeConfig::class);
    }
}
