<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}