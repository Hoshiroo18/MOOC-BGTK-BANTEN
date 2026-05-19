<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaKegiatan extends Model
{
    protected $table      = 'peserta_kegiatan';
    protected $primaryKey = 'peserta_kegiatan_id';

    protected $fillable = [
        'peserta_id',
        'kegiatan_id',
        'status',
        'terdaftar_at',
    ];

    protected $casts = [
        'terdaftar_at' => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'peserta_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id', 'kegiatan_id');
    }
}
