<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moda extends Model
{
    protected $table      = 'moda';
    protected $primaryKey = 'moda_id';
    public $timestamps    = false;
    protected $fillable   = ['jenis_moda'];
}
