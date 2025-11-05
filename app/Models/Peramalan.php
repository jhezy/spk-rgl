<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peramalan extends Model
{
    protected $table = 'peramalan';
    protected $primaryKey = 'id_peramalan';
    protected $fillable = [
        'periode_mulai',
        'periode_akhir',
        'forecast_bulan',
        'a',
        'b1',
        'b2',
        'forecast_value'
    ];
    protected $dates = ['periode_mulai', 'periode_akhir', 'forecast_bulan'];
    public $timestamps = true;
}
