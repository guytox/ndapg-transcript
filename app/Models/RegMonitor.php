<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegMonitor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function RegMonitorItems(){

        return $this->hasMany(RegMonitorItems::class,'monitor_id','id');

    }
    

    public function student(){

        return $this->belongsTo(StudentRecord::class,'student_id');

    }


}
