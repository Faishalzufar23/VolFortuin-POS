<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use App\Models\Ingredient;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function afterCreate(): void
    {
        $sale = $this->record;

        foreach ($sale->items as $item) {
            $product = $item->product;

            foreach ($product->productIngredients as $pi) {

                $ingredient = $pi->ingredient;

                $usedQty = $pi->quantity * $item->quantity;
                // ex: 5 gram per produk × 3 produk terjual

                $ingredient->decrement('stock', $usedQty);
            }
        }
    }
}
