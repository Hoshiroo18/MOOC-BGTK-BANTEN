<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table      = 'sekolah';
    protected $primaryKey = 'sekolah_id';
    public $timestamps    = false;

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'jenjang',
        'bentuk_pendidikan',
        'status_sekolah',
        'akreditasi',
        'alamat',
        'kab_kota',
        'desa',
        'kecamatan',
        'akses_internet',
    ];
}
