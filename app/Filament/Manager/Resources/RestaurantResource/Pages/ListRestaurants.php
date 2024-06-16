<?php

namespace App\Filament\Manager\Resources\RestaurantResource\Pages;

use App\Filament\Manager\Resources\RestaurantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestaurants extends ListRecords
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
