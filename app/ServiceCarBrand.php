<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCarBrand extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_type_id',
        'car_categories_id',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'created_at', 'updated_at'
    ];

    public function cars()
    {
        return $this->belongsTo('App\CarCategory','car_categories_id','id');
    }

    /**
     * The services that belong to the user.
     */
    public function service_type()
    {
        return $this->belongsTo('App\ServiceType','service_type_id','id');
    }

}
