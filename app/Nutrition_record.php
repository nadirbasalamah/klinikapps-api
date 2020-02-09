<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Nutrition_record extends Model
{
    use Authenticatable, Authorizable;

    protected $table = 'nutrition_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_patient','bb','tb','lila','imt','bbi','status','fat','visceral_fat','muscle',
        'body_age','gda','gdp','gd2jpp','asam_urat','trigliserida','kolesterol','ldl','hdl','ureum',
        'kreatinin','sgot','sgpt','tensi','rr','suhu','lainnya','oedema','aktivitas','durasi_olahraga',
        'jenis_olahraga','diagnosa_dahulu','diagnosa_skrg','nafsu_makan','frekuensi_makan','alergi','makanan_kesukaan',
        'dietary_nasi','dietary_lauk_hewani','dietary_lauk_nabati','dietary_sayur','dietary_sumber_minyak','dietary_minuman',
        'dietary_softdrink','dietary_jus','dietary_suplemen','dietary_lainnya','lain_lain','diagnosa','angka_tb_bb',
        'gambar_tb_bb','keterangan_tb_bb','angka_bb_u','gambar_bb_u','keterangan_bb_u','angka_tb_u'
        ,'gambar_tb_u','keterangan_tb_u','angka_imt_u','gambar_imt_u','keterangan_imt_u',
        'angka_hc_u','gambar_hc_u','keterangan_hc_u','energi','persen_karbohidrat','gram_karbohidrat',
        'persen_protein','gram_protein','persen_lemak','gram_lemak','mon_date','result'
    ];
}
