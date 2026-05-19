<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table    = 'kegiatan';
    protected $primaryKey = 'kegiatan_id';

    protected $fillable = [
        'tipe_kegiatan_id',
        'jenis_kegiatan_id',
        'moda_id',
        'kuota',
        'is_registration_required',
        'waktu_pelaksanaan',
        'start_date',
        'end_date',
        'token_kegiatan',
        'status_url',
        'nama_kegiatan',
        'deskripsi',
        'link_zoom',
        'link_lms',
        'flayer',
        'slug',
        'link_pendaftaran',
    ];

    protected $casts = [
        'waktu_pelaksanaan' => 'datetime',
        'start_date'        => 'date',
        'end_date'          => 'date',
        'is_registration_required' => 'boolean',
    ];

    // Relasi ke tabel tipe_kegiatan
    public function tipeKegiatan()
    {
        return $this->belongsTo(TipeKegiatan::class, 'tipe_kegiatan_id', 'tipe_kegiatan_id');
    }

    // Relasi ke tabel jenis_kegiatan
    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatan::class, 'jenis_kegiatan_id', 'jenis_kegiatan_id');
    }

    // Relasi ke tabel moda
    public function moda()
    {
        return $this->belongsTo(Moda::class, 'moda_id', 'moda_id');
    }

    // Relasi ke fasilitator via fasilitator_mapping
    public function fasilitators()
    {
        return $this->belongsToMany(
            Fasilitator::class,
            'fasilitator_mapping',
            'kegiatan_id',
            'fasilitator_id',
            'kegiatan_id',
            'fasilitator_id'
        );
    }

    // Helper untuk cek status aktif
    public function isActive()
    {
        return $this->status_url === 'active';
    }

    // Scope untuk filter kegiatan aktif
    public function scopeActive($query)
    {
        return $query->where('status_url', 'active');
    }

    // Scope untuk filter kegiatan nonaktif
    public function scopeInactive($query)
    {
        return $query->where('status_url', 'inactive');
    }
}
