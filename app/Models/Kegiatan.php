<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $fillable = [
        'jenis_kegiatan',
        'moda',
        'fasil',
        'kuota',
        'waktu_pelaksanaan',
        'nama_kegiatan',
        'deskripsi',
        'link_zoom',
        'flayer',
        'slug',
        'link_pendaftaran',
    ];
}