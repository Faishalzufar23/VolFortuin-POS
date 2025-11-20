<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Filament\Resources\Products\RelationManagers\ProductIngredientsRelationManager;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }



    // public static function canViewAny(): bool
    // {
    //     return (Auth::user()?->role ?? '') === 'admin';
    // }

    // public static function canEdit($record): bool
    // {
    //     return (Auth::user()?->role ?? '') === 'admin';
    // }

    // public static function canDelete($record): bool
    // {
    //     return (Auth::user()?->role ?? '') === 'admin';
    // }

    // public static function canCreate(): bool
    // {
    //     return (Auth::user()?->role ?? '') === 'admin';
    // }

    public static function getRelations(): array
    {
        return [
            ProductIngredientsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
