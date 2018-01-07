<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_address',
        'from_city',
        'to_address',
        'to_city',
        'latitude',
        'longitude'
    ];
    
     /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'created_at', 'updated_at'
    ];


}
