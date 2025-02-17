<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PrItemDetail extends Model
{
    use LogsActivity;
    use HasFactory;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'item_id', 'description', 'uom', 'website_link', 'original_price', 'markup_price'
    ];

    public function item()
    {
        return $this->belongsTo(PrItem::class, 'item_id');
    }

}
