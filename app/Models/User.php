<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_name',
        'nama',
        'password',
        'nip',
        'nik',
        'email',
        'no_urut',
        'no_hp',
        'role_id',
        'tim_kerja_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Override untuk support login dengan email ATAU user_name
     * Dipakai oleh Auth::attempt()
     */
    public function getAuthIdentifierName(): string
    {
        return 'user_id';
    }
}
