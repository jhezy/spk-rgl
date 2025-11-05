<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $fillable = ['bulan', 'jumlah_penjualan'];
    protected $dates = ['bulan'];
    public $timestamps = true;
}
