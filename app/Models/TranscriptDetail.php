<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranscriptDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function nationality(){
        return $this->belongsTo(Country::class, 'country','id');
    }
}
