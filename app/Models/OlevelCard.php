<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OlevelCard extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        /**
         * saving date registered when we create a user.
         * @return void
         */
        OlevelCard::creating(function ($model) {
            $model->exam_body = explode($model->exam_type, ' ')[0];
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
