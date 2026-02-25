<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MonthlyBalance extends Model
{
    use HasUuids;

    protected $table = 'monthly_balances';

    public $timestamps = false;

    protected $fillable = [
        'month',
        'year',
        'total_income',
        'total_expense',
        'ending_balance',
        'created_at',
    ];

    protected $casts = [
        'month'          => 'integer',
        'year'           => 'integer',
        'total_income'   => 'decimal:0',
        'total_expense'  => 'decimal:0',
        'ending_balance' => 'decimal:0',
        'created_at'     => 'datetime',
    ];
}
