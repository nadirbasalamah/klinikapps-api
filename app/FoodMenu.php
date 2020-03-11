<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class FoodMenu extends Model
{
    protected $table = 'food_menu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_patient','breakfast','breakfast_time','lunch','lunch_time',
        'dinner','dinner_time'
    ];
}
