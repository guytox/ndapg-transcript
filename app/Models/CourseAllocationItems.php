<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAllocationItems extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function courseAllocation(){
        return $this->belongsTo(CourseAllocationMonitor::class, 'allocation_id');
    }
}
