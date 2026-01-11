<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->required()
                ->searchable()
                ->preload(),


            TextInput::make('name')
                ->label('Nama Produk')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('sku')
                ->label('SKU')
                ->required()
                ->unique(ignoreRecord: true),

            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3),

            TextInput::make('price')
                ->label('Harga')
                ->prefix('IDR')
                ->numeric()
                ->required(),

            /* =========================
             | PREVIEW FOTO (KECIL & RAPI)
             ========================= */
            Placeholder::make('image_preview')
                ->label('Foto Saat Ini')
                ->content(
                    fn($record) => $record && $record->image
                        ? new HtmlString(
                            '<img
                            src="' . asset('storage/' . $record->image) . '"
                            class="w-32 h-32 object-cover rounded-lg border shadow"
                        >'
                        )
                        : new HtmlString('<span class="text-gray-500">Belum ada foto</span>')
                ),

            /* =========================
             | BUTTON GANTI FOTO
             ========================= */
            FileUpload::make('image')
                ->label('Ganti Foto')
                ->image()
                ->disk('public')
                ->directory('products')
                ->visibility('public')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(2048)

                // 🔥 INI KUNCI UTAMA
                ->dehydrated(fn($state) => filled($state))
                ->previewable(false) // ⛔ tidak tampil preview besar
                ->helperText('Klik tombol untuk memilih gambar baru'),
        ]);
    }
}
