<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrSignatory extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'cash_availability',
        'approved_by',
        'requested_by_position',
        'cash_availability_position',
        'approved_by_position',
    ];
}
