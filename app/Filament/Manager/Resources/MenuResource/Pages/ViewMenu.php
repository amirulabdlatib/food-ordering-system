<?php

namespace App\Filament\Manager\Resources\MenuResource\Pages;

use App\Filament\Manager\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMenu extends ViewRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
