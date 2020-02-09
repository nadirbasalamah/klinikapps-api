<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_patient','rm_number','rmgizi_number','visitdate','referral','fullname','age','gender',
        'address','phone_number','email','birthdate','profile_picture','education','job','religion'
    ];
}
