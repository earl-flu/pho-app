<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class RequestedItem extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['item_name', 'category_name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getItemNameAttribute()
    {
        return $this->itemDetail->item->name ?? 'Unknown Item';
    }

    public function getCategoryNameAttribute()
    {
        return $this->itemDetail->item->category->name ?? 'Unknown Category';
    }

    //Description/specs of the item
    public function itemDetail()
    {
        return $this->belongsTo(PrItemDetail::class, 'item_detail_id');
    }

}
