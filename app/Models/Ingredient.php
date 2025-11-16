<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    public function productIngredients()
    {
        return $this->hasMany(\App\Models\ProductIngredient::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
            ->withPivot('quantity', 'unit');
    }

    protected $fillable = [
        'name',
        'unit',
        'stock',
    ];
}
