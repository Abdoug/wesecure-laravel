<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $table = 'keys';

    protected $fillable = [
        'user_id', 'date', 'public_key'
    ];
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
