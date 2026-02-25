<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ResidentHouseHistory extends Model
{
    use HasUuids;

    protected $table = 'resident_house_histories';

    public $timestamps = false;

    protected $fillable = [
        'resident_id',
        'house_id',
        'move_in_date',
        'move_out_date',
        'is_active',
        'created_at',
    ];

    protected $casts = [
        'move_in_date'  => 'date',
        'move_out_date' => 'date',
        'is_active'     => 'boolean',
        'created_at'    => 'datetime',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
