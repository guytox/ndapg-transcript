<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(State::class, 'department_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function sex(){
        return $this->belongsTo(Gender::class, 'gender','id');
    }
}
