<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKegiatan extends Model
{
    protected $table      = 'jenis_kegiatan';
    protected $primaryKey = 'jenis_kegiatan_id';
    public $timestamps    = false;
    protected $fillable   = ['jenis_kegiatan'];
}
