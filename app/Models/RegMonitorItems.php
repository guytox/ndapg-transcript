<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegMonitorItems extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function RegMonitor(){

        return $this->belongsTo(RegMonitor::class, 'monitor_id','id');
    }

    public function semesterCourse(){
        return $this->belongsTo(SemesterCourse::class, 'course_id','id');
    }
}
