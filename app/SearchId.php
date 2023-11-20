<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchId extends Model
{
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
