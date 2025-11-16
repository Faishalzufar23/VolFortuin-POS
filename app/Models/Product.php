<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'image',
    ];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function productIngredients()
    {
        return $this->hasMany(ProductIngredient::class);
        return $this->hasMany(\App\Models\ProductIngredient::class);
    }


    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
            ->withPivot('quantity', 'unit');
    }
}
