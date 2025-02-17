<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class PurchaseRequest extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['budget_name', 'budget_remaining'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getBudgetNameAttribute()
    {
        return $this->budget->name ?? 'Unknown Budget';
    }

    public function getBudgetRemainingAttribute()
    {
        return $this->budget->amount;
    }


    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function items()
    {
        return $this->hasMany(RequestedItem::class, 'purchase_request_id');
    }

}
