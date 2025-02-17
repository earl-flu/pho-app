<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PrItem extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $fillable = ['category_id', 'name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function category()
    {
        return $this->belongsTo(PrCategory::class, 'category_id');
    }

    public function details()
    {
        return $this->hasMany(PrItemDetail::class, 'item_id');
    }
}
