<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'sm' => 2,
                'md' => 3,
                'xl' => 4,
            ])
            ->columns([
                Stack::make([

                    ImageColumn::make('image')
                        ->height(160)
                        ->square()
                        ->alignCenter()
                        ->extraImgAttributes([
                            'class' => 'object-cover rounded-xl',
                        ])
                        ->getStateUsing(
                            fn($record) =>
                            $record->image
                                ? asset('storage/' . $record->image)
                                : asset('images/no-image.png')
                        ),

                    TextColumn::make('name')
                        ->weight('bold')
                        ->size('lg')
                        ->alignCenter(),

                    TextColumn::make('description')
                        ->limit(60)
                        ->color('gray')
                        ->alignCenter()
                        ->wrap(),

                    TextColumn::make('price')
                        ->money('IDR')
                        ->color('success')
                        ->alignCenter(),

                    TextColumn::make('stock')
                        ->badge()
                        ->alignCenter()
                        ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                        ->formatStateUsing(
                            fn($state) =>
                            $state > 0 ? "Stok: $state" : 'Stok Habis'
                        ),
                ])->space(2),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->button(),
            ])
            ->recordClasses([
                'bg-white',
                'rounded-xl',
                'shadow-sm',
                'hover:shadow-lg',
                'transition',
                'p-4',
            ])
            ->defaultSort('name');
    }
}
