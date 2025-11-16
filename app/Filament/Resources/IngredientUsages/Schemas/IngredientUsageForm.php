<?php

namespace App\Filament\Resources\IngredientUsages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IngredientUsageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sale_id')
                    ->required()
                    ->numeric(),
                TextInput::make('product_id')
                    ->required()
                    ->numeric(),
                TextInput::make('ingredient_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quantity_used')
                    ->required()
                    ->numeric(),
                TextInput::make('unit')
                    ->required(),
            ]);
    }
}
