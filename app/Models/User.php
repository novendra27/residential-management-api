<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'created_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'password'   => 'hashed',
    ];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
