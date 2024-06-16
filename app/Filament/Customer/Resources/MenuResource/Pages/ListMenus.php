<?php

namespace App\Filament\Customer\Resources\MenuResource\Pages;

use App\Filament\Customer\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}