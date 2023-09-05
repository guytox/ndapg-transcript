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

    public function regItems(){
        return $this->hasMany(RegMonitorItems::class, 'student_id', 'student_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function student(){
        return $this->belongsTo(StudentRecord::class, 'student_id','id');
    }
}
