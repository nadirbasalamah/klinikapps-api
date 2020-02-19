<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Biochemistry extends Model
{
    use Authenticatable, Authorizable;

    protected $table = 'biochemistry';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_patient','gda','gdp','gd2jpp','asam_urat','trigliserida','kolesterol','ldl','hdl','ureum',
        'kreatinin','sgot','sgpt'
    ];
}
