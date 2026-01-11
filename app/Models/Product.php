<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'image',
    ];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'product_id');
    }

    public function productIngredients()
    {
        return $this->hasMany(ProductIngredient::class);
    }

    // Relasi pivot ke tabel product_ingredients
    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'product_ingredients',
            'product_id',
            'ingredient_id'
        )->withPivot(['quantity', 'unit'])
            ->withTimestamps();
    }

    // HPP per porsi
    public function hppPerPorsi()
    {
        return $this->ingredients->sum(function ($ingredient) {
            return ($ingredient->pivot->quantity ?? 0) * ($ingredient->price ?? 0);
        });
    }

    // Total HPP
    public function hppTotal()
    {
        return $this->hppPerPorsi() * $this->saleItems()->sum('quantity');
    }

    // Total Penjualan
    public function totalSales()
    {
        return $this->saleItems()->sum('line_total');
    }

    // Laba Kotor
    public function profit()
    {
        return $this->totalSales() - $this->hppTotal();
    }

    public function getStockAttribute()
    {
        $recipes = $this->productIngredients()->with('ingredient')->get();

        if ($recipes->isEmpty()) return 0;

        $min = null;

        foreach ($recipes as $r) {
            if (!$r->ingredient || $r->quantity <= 0) continue;

            $available = floor($r->ingredient->stock / $r->quantity);

            if ($min === null || $available < $min) {
                $min = $available;
            }
        }

        return $min ?? 0;
    }

    public function getHasImageAttribute(): bool
    {
        return ! empty($this->image);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
