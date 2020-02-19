<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Clinic extends Model
{
    use Authenticatable, Authorizable;

    protected $table = 'clinic';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_patient','tensi','rr','suhu','lainnya','oedema','aktivitas','durasi_olahraga',
        'jenis_olahraga','diagnosa_dahulu','diagnosa_skrg'
    ];
}
