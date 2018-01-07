<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'provider_name',
        'image',
        'price',
        'fixed',
        'outer_fixed',
        'description',
        'status',
        'minute',
        'distance',
        'connection',
        'day',
        'ride',
        'calculator',
        'capacity',
        'outer_distance',
        'outer_price'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'created_at', 'updated_at'
    ];

    /**
     * The services that belong to the user.
     */
    public function service_cars()
    {
        return $this->hasMany('App\ServiceCarBrand','service_type_id','id')->count();
    }

}
