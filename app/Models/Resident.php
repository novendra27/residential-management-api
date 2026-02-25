<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'ktp_photo',
        'is_contract',
        'phone_number',
        'is_married',
        'created_at',
    ];

    protected $casts = [
        'is_contract' => 'boolean',
        'is_married'  => 'boolean',
        'created_at'  => 'datetime',
    ];

    public function houseHistories()
    {
        return $this->hasMany(ResidentHouseHistory::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
