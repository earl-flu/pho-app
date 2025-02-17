<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetChange extends Model
{
    use HasFactory;
    protected $fillable = ['budget_id', 'amount', 'operation', 'remarks', 'user_id'];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
