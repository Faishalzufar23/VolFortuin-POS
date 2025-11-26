<?php

namespace App\Filament\Resources\FinanceReports;

use App\Filament\Resources\FinanceReports\Pages\CreateFinanceReport;
use App\Filament\Resources\FinanceReports\Pages\EditFinanceReport;
use App\Filament\Resources\FinanceReports\Pages\ListFinanceReports;
use App\Filament\Resources\FinanceReports\Pages\ViewFinanceReport;
use App\Filament\Resources\FinanceReports\Schemas\FinanceReportForm;
use App\Filament\Resources\FinanceReports\Schemas\FinanceReportInfolist;
use App\Filament\Resources\FinanceReports\Tables\FinanceReportsTable;
use App\Models\FinanceReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinanceReportResource extends Resource
{
    protected static ?string $model = null;

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    protected static ?string $pluralLabel = 'Laporan Keuangan';
    protected static ?string $modelLabel = 'Laporan Keuangan';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;


    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FinanceReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FinanceReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceReportsTable::configure($table);
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
            'index' => ListFinanceReports::route('/'),
        ];
    }
}
