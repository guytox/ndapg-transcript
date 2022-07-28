<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingSystem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function gradingSystemItems(){
        return $this->hasMany(GradingSystemItems::class, 'grading_system_id');
    }
}
