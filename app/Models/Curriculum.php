<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function curriculumItems()
    {
        return $this->hasMany(CurriculumItem::class);
    }
}
