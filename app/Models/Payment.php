<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'bill_id',
        'payment_date',
        'amount_paid',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid'  => 'decimal:0',
        'created_at'   => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
