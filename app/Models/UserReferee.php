<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReferee extends Model
{
    use HasFactory;
    protected $guarded;

    public static function generateUniqueRefUID()
    {
        do {
            $code = uniqid('ref_');
        } while (UserReferee::where("uid", "=", $code)->first());

        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        /**
         * saving date registered when we create a user.
         * @return void
         */
        UserReferee::creating(function ($model) {
            $model->uid = self::generateUniqueRefUID();
            $model->expiry_date = now()->addDays(2);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
