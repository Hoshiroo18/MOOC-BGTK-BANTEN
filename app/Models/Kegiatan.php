<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $fillable = [
        'jenis_kegiatan',
        'jenis_pelatihan',
        'perlu_pendaftaran',
        'moda',
        'fasil',
        'kuota',
        'waktu_pelaksanaan',
        'nama_kegiatan',
        'deskripsi',
        'link_zoom',
        'moodle_course_url',
        'flayer',
        'slug',
        'link_pendaftaran',
    ];

    protected $casts = [
        'perlu_pendaftaran' => 'boolean',
        'waktu_pelaksanaan' => 'datetime',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'kegiatan_id');
    }
}