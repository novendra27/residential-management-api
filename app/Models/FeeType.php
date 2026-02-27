<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasUuids;

    protected $table = 'fee_types';

    public $timestamps = false;

    protected $fillable = [
        'fee_name',
        'default_amount',
        'created_at',
    ];

    protected $casts = [
        'default_amount' => 'decimal:0',
        'created_at'     => 'datetime',
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
