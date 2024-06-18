<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserTypeOverview extends ChartWidget
{
    protected static ?string $heading = 'User Category';

    protected static ?int $sort = 3;


    protected function getData(): array
    {
        $userCategoryCounts = User::groupBy('role')
            ->selectRaw('role, count(*) as count')
            ->pluck('count', 'role')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'User Categories',
                    'data' => array_values($userCategoryCounts),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(159, 90, 253, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                    ],
                ],
            ],
            'labels' => array_keys($userCategoryCounts),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}