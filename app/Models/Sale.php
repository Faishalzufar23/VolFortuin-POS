<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;
use App\Models\User;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'items_count',
        'sub_total',
        'tax',
        'discount',
        'total',
        'payment_status',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
