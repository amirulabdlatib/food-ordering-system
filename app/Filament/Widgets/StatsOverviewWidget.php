<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\User;
use App\Models\Restaurant;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class StatsOverviewWidget extends BaseWidget
{
    //live updating stats - by default every 5 seconds
    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;


    protected function getStats(): array
    {
        return [
            //
            Stat::make('Revenue (RM)', Sale::sum('total_sales'))
                    ->chart([7, 2, 10, 3, 15, 4, 17,20,30,40])
                    ->color('success')
            ,
            Stat::make('Active Restaurant', Restaurant::where('status',true)->where('is_approved',true)->count())
                        ->chart([7, 2, 10, 3, 15, 4, 17])
                        ->color('info'),
            Stat::make('Registered Users',User::where('role', '!=', 'admin')->count())
                        ->description('Managers and Customers')
                        ->descriptionIcon('heroicon-o-users')
                        ->chart([7, 2, 10, 3, 15, 4, 17])
                        ->color('grey')
                        ,
        ];
    }
}