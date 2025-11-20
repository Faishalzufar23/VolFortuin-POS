<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Simpan Produk')
                ->color('success')
                ->after(function ($record) {
                    if ($record->productIngredients()->count() === 0) {
                        $record->delete();
                        $this->addError('productIngredients', 'Tambahkan minimal 1 bahan resep terlebih dahulu.');
                        $this->halt();
                    }
                }),
        ];
    }
}
