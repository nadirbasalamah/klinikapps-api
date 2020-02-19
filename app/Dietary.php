<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Dietary extends Model
{
    use Authenticatable, Authorizable;

    protected $table = 'dietary';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_patient','nafsu_makan','frekuensi_makan','alergi','makanan_kesukaan',
        'dietary_nasi','dietary_lauk_hewani','dietary_lauk_nabati','dietary_sayur','dietary_sumber_minyak','dietary_minuman',
        'dietary_softdrink','dietary_jus','dietary_suplemen','dietary_lainnya','lain_lain'
    ];
}
