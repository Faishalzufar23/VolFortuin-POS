<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nama Produk')
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->required(),

            TextInput::make('sku')
                ->label('SKU')
                ->unique(ignoreRecord: true)
                ->required(),

            Textarea::make('description')
                ->label('Deskripsi')
                ->columnSpanFull(),

            TextInput::make('price')
                ->label('Harga')
                ->prefix('IDR')
                ->numeric()
                ->required(),

            FileUpload::make('photo')
                ->label('Foto Produk')
                ->image()
                ->directory('products')
                ->preserveFilenames()
                ->visibility('public')





        ]);
    }
}
