<?php

namespace App\Filament\Resources\IngredientPurchases\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class IngredientPurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ingredient.name')
                    ->label('Bahan Baku')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->sortable(),

                TextColumn::make('ingredient.unit')
                    ->label('Satuan')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal & Jam')
                    ->dateTime('d M Y - H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
            ])

            ->filters([
                Filter::make('tanggal')
                    ->label('Filter Tanggal')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn ($q, $date) => $q->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn ($q, $date) => $q->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
