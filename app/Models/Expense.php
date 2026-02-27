<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'expense_name',
        'expense_date',
        'amount',
        'description',
        'is_monthly',
        'created_at',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:0',
        'is_monthly'   => 'boolean',
        'created_at'   => 'datetime',
    ];
}
