<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingGraduant extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function regMonitor(){
        return $this->belongsTo(RegMonitor::class, 'result_id', 'id');
    }
}
