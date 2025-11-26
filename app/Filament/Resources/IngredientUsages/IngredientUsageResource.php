<?php

namespace App\Filament\Resources\IngredientUsages;

use App\Filament\Resources\IngredientUsages\Pages\CreateIngredientUsage;
use App\Filament\Resources\IngredientUsages\Pages\EditIngredientUsage;
use App\Filament\Resources\IngredientUsages\Pages\ListIngredientUsages;
use App\Filament\Resources\IngredientUsages\Schemas\IngredientUsageForm;
use App\Filament\Resources\IngredientUsages\Tables\IngredientUsagesTable;
use App\Models\IngredientUsage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms;
use Filament\Tables;
use Filament\Filament\Forms\Components\DatePicker;





class IngredientUsageResource extends Resource
{
    protected static ?string $model = IngredientUsage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Gudang';

    protected static ?string $navigationLabel = 'Laporan Pemakaian';
    protected static ?string $pluralLabel = 'Laporan Pemakaian';
    protected static ?string $modelLabel = 'Laporan Pemakaian';

    protected static ?string $recordTitleAttribute = 'ingredient.name';

    public static function form(Schema $schema): Schema
    {
        return IngredientUsageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('product.name')
                    ->label('Produk'),

                TextColumn::make('ingredient.name')
                    ->label('Bahan Baku'),

                TextColumn::make('quantity_used')
                    ->label('Jumlah Digunakan')
                    ->formatStateUsing(fn ($state) => rtrim(rtrim($state, '0'), '.')),

                TextColumn::make('unit')
                    ->label('Satuan'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIngredientUsages::route('/'),
            'create' => CreateIngredientUsage::route('/create'),
            'edit' => EditIngredientUsage::route('/{record}/edit'),
        ];
    }
}
