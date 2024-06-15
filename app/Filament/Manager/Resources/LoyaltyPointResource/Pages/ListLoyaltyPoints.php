<?php

namespace App\Filament\Manager\Resources\LoyaltyPointResource\Pages;

use App\Filament\Manager\Resources\LoyaltyPointResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyPoints extends ListRecords
{
    protected static string $resource = LoyaltyPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
