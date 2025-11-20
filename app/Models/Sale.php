<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    'payment_method',
    'paid_amount',
    'change_amount'
];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function ingredientUsages()
    {
        return $this->hasMany(IngredientUsage::class);
    }
}
