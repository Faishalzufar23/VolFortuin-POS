<?php

namespace App\Filament\Resources\IngredientPurchases\Schemas;

use App\Models\Ingredient;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class IngredientPurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('ingredient_id')
                ->label('Bahan Baku')
                ->relationship('ingredient', 'name')
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $ingredient = Ingredient::find($state);
                    $set('unit', $ingredient?->unit);
                })
                ->preload()
                ->searchable(),

            TextInput::make('quantity')
                ->label('Jumlah')
                ->numeric()
                ->required(),

            TextInput::make('unit')
                ->label('Satuan')
                ->disabled(),

            TextInput::make('total_cost')
                ->label('Total Harga Pembelian')
                ->numeric()
                ->required(),


            Hidden::make('created_at')
                ->default(fn() => Carbon::now()),

        ]);
    }
}
