<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Budget extends Model
{
    use LogsActivity;
    use HasFactory;
    protected $fillable = ['name', 'amount'];

    const OPERATION_ADD = 'add';
    const OPERATION_SUBTRACT = 'subtract';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }
}
