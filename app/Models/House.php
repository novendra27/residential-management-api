<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'house_number',
        'address',
        'is_occupied',
        'created_at',
    ];

    protected $casts = [
        'is_occupied' => 'boolean',
        'created_at'  => 'datetime',
    ];

    public function houseHistories()
    {
        return $this->hasMany(ResidentHouseHistory::class);
    }

    public function currentResident()
    {
        return $this->hasOne(ResidentHouseHistory::class)->where('is_active', true)->with('resident');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
