<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $guarded = [];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'moodle_injected_at' => 'datetime',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function peserta()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}