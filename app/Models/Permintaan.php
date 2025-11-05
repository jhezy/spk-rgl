<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'id_permintaan';
    protected $fillable = ['bulan', 'jumlah_permintaan'];
    protected $dates = ['bulan'];
    public $timestamps = true;
}
