<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
