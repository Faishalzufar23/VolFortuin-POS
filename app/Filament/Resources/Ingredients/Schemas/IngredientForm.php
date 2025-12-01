<?php

namespace App\Filament\Resources\Ingredients\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\IngredientPurchase;

class IngredientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('name')
                ->label('Nama Bahan')
                ->required()
                ->unique(ignorable: fn($record) => $record),

            Select::make('unit')
                ->label('Satuan')
                ->options([
                    'gram' => 'Gram',
                    'ml' => 'mL',
                    'pcs' => 'Pcs',
                ])
                ->required(),

            TextInput::make('stock')
                ->label('Stok (otomatis)')
                ->disabled()
                ->default(0)
                ->numeric(),


        ]);
    }
}
