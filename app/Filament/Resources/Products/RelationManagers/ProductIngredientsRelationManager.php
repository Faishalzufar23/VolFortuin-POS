<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Ingredient;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Schemas\Schema;

// ⚡ ACTIONS YANG BENAR UNTUK FILAMENT 4.2
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;

class ProductIngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'productIngredients';

    protected static ?string $recordTitleAttribute = 'ingredient.name';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('ingredient_id')
                ->label('Bahan Baku')
                ->options(Ingredient::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('quantity')
                ->label('Jumlah / Produk')
                ->numeric()
                ->inputMode('decimal')
                ->required(),

            Forms\Components\TextInput::make('unit')
                ->label('Satuan'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ingredient.name')
                ->label('Bahan'),

                Tables\Columns\TextColumn::make('quantity')
                ->label('Jumlah')
                ->formatStateUsing(fn ($state) => rtrim(rtrim($state, '0'), '.')),

                Tables\Columns\TextColumn::make('unit')
                ->label('Satuan'),
            ])

            // ⚡ HEADER ACTION YANG BENAR
            ->headerActions([
                Action::make('create_resep')
                    ->label('Tambah Resep')
                    ->color('success')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\Select::make('ingredient_id')
                            ->label('Bahan')
                            ->options(Ingredient::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah / Produk')
                            ->inputMode('decimal')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('unit')
                            ->label('Satuan'),
                    ])
                    ->action(function (array $data) {
                        // Simpan data resep ke productIngredients
                        $this->ownerRecord
                            ->productIngredients()
                            ->create($data);
                    })
            ])

            // ⚡ EDIT
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            // ⚡ BULK DELETE
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
