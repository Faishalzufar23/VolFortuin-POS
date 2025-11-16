<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientUsage extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'ingredient_id',
        'quantity_used',
        'unit',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(\App\Models\Sale::class);
    }
}
