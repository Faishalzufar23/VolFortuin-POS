<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->color('success')
                ->action(function () {
                    $record = $this->record;

                    if ($record->productIngredients()->count() === 0) {
                        $this->addError('productIngredients', 'Produk wajib memiliki minimal 1 bahan.');
                        return;
                    }

                    $this->save();
                }),

            Action::make('delete')
                ->label('Hapus Produk')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn() => $this->delete()),
        ];
    }
}
