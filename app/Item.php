<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function image()
    {
        return $this->hasMany('App\Image');
    }

    public function searchId()
    {
        return $this->hasOne('App\SearchId');
    }
    
    public function getBuyerNameAttribute($value)
    {
        return ucfirst($value);
    }
}
