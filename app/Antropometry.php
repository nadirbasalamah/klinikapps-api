<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Antropometry extends Model
{
    use Authenticatable, Authorizable;

    protected $table = 'antropometry';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_patient','bb','tb','lila','imt','bbi','status','fat','visceral_fat','muscle','body_age'
    ];
}
