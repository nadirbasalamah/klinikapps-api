<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Interenvention extends Model
{
    use Authenticatable, Authorizable;

    protected $table = 'interenvention';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id_patient','energi','persen_karbohidrat','gram_karbohidrat',
        'persen_protein','gram_protein','persen_lemak','gram_lemak'
    ];
}
