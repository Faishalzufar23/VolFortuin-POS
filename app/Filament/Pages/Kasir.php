<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Kasir extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = 'Kasir';
    protected static ?string $title = 'Kasir POS';

    // ❗ INGAT: TIDAK STATIC!
    protected string $view = 'filament.pages.kasir';
}
