<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitator extends Model
{
    protected $table      = 'fasilitator';
    protected $primaryKey = 'fasilitator_id';
    public $timestamps    = false;
    protected $fillable   = [
        'user_id',
        'no_urut',
        'tim_kerja_id',
        'nip',
        'nama',
        'pangkat',
        'golongan',
        'jabatan',
        'jenis_jabatan',
        'status_kepegawaian',
        'lokasi_kantor',
        'no_hp',
        'status',
    ];
}
