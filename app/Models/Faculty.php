<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;

class Faculty extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function department()
    {
        return $this->hasMany(Department::class, 'faculty_id');
    }
}
