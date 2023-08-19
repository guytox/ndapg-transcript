<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAllocationMonitor extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function allocationItems(){
        return $this->hasMany(CourseAllocationItems::class, 'allocation_id','id');
    }

    public function department(){
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
