<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQualification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function generateUniqueCert()
    {
        do {
            $code = uniqid('cert_');
        } while (UserQualification::where("uid", "=", $code)->first());

        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        /**
         * saving date registered when we create a user.
         * @return void
         */
        UserQualification::creating(function ($model) {
            $model->uid = self::generateUniqueCert();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class,'id','user_id');
    }
}
