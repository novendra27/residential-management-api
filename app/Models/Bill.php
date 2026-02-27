<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'house_id',
        'resident_id',
        'fee_type_id',
        'period_start',
        'period_end',
        'total_amount',
        'is_paid',
        'created_at',
    ];

    protected $casts = [
        'period_start'  => 'date',
        'period_end'    => 'date',
        'total_amount'  => 'decimal:0',
        'is_paid'       => 'boolean',
        'created_at'    => 'datetime',
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
