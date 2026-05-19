<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeKegiatan extends Model
{
    protected $table      = 'tipe_kegiatan';
    protected $primaryKey = 'tipe_kegiatan_id';
    public $timestamps    = false;
    protected $fillable   = ['nama_kegiatan'];
}
