<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeTemplateItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function feeTemplate() {

        return $this->belongsTo(FeeTemplate::class);

    }
}
