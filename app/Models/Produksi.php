<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksi';
    protected $primaryKey = 'id_produksi';
    protected $fillable = ['bulan', 'jumlah_produksi'];
    protected $dates = ['bulan'];
    public $timestamps = true;
}
