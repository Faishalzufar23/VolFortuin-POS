<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientPurchase extends Model
{
    protected $fillable = [
        'ingredient_id',
        'quantity',
        'unit',
        'total_cost',
        'note',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    protected static function booted()
    {
        // Setiap kali pembelian dibuat
        static::created(function ($purchase) {
            $ingredient = $purchase->ingredient;

            // Tambah stok
            $ingredient->increment('stock', $purchase->quantity);

            // Hitung harga per unit (gram / ml / pcs)
            if ($purchase->quantity > 0) {
                $ingredient->price = $purchase->total_cost / $purchase->quantity;
                $ingredient->save();
            }
        });

        // Jika pembelian diperbarui
        static::updated(function ($purchase) {
            $ingredient = $purchase->ingredient;

            // Update stok
            $old = $purchase->getOriginal('quantity');
            $diff = $purchase->quantity - $old;
            $ingredient->increment('stock', $diff);

            // Update harga bahan
            if ($purchase->quantity > 0) {
                $ingredient->price = $purchase->total_cost / $purchase->quantity;
                $ingredient->save();
            }
        });

        // Jika pembelian dihapus
        static::deleted(function ($purchase) {
            $purchase->ingredient->decrement('stock', $purchase->quantity);
        });
    }
}
