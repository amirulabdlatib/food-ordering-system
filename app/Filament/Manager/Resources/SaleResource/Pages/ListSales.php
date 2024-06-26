<?php

namespace App\Filament\Manager\Resources\SaleResource\Pages;

use App\Filament\Manager\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}