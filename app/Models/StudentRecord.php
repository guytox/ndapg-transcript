<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRecord extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function uploadedPayments(){

        return $this->hasMany(UploadedPayment::class, 'student_id','id');
    }

    public function programme(){
        return $this->belongsTo(Program::class, 'id','program_id');
    }

    public function defferment(){
        return $this->hasMany(Defferment::class, 'student_id', 'id');
    }
}
