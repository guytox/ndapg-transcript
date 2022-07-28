<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingSystemItems extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function gradingsystem(){
        return $this->belongsTo(GradingSystem::class);
    }
}
