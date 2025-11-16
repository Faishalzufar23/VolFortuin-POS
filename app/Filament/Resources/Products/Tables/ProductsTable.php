<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama'),
                Tables\Columns\TextColumn::make('price')->label('Harga'),
                Tables\Columns\TextColumn::make('stock')->label('Stok'),
                Tables\Columns\ImageColumn::make('image')->label('Foto')->rounded(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
