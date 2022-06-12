<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OlevelResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'exam_details' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        /**
         * saving date registered when we create a user.
         * @return void
         */
        OlevelResult::creating(function ($model) {
            $model->uid = uniqid('olr_');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
