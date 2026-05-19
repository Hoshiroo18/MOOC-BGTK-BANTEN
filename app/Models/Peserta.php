<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table      = 'peserta';
    protected $primaryKey = 'peserta_id';
    public $timestamps    = true;

    // kegiatan_id sudah TIDAK ADA di tabel peserta (dipindah ke peserta_kegiatan)
    protected $fillable = [
        'nama',
        'nip',
        'nik',
        'email',
        'jenis_kelamin',
        'tanggal_lahir',
        'kota_id',
        'sekolah_id',
        'Instansi',
    ];

    // Relasi ke kegiatan via pivot peserta_kegiatan
    public function kegiatans()
    {
        return $this->belongsToMany(
            Kegiatan::class,
            'peserta_kegiatan',
            'peserta_id',
            'kegiatan_id',
            'peserta_id',
            'kegiatan_id'
        )->withPivot('peserta_kegiatan_id', 'status', 'terdaftar_at')
            ->withTimestamps();
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }

    public function kota()
    {
        return $this->belongsTo(Kota::class, 'kota_id', 'kota_id');
    }
}
