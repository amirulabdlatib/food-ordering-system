<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class ChartOverview extends ChartWidget
{
    protected static ?string $heading = 'Order Type Distribution';
    protected static ?int $sort = 2;



    protected function getData(): array
    {
        $orderTypeCounts = Order::groupBy('order_type')->selectRaw('order_type, count(*) as count')->pluck('count', 'order_type')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Order Types',
                    'data' => array_values($orderTypeCounts),
                    'backgroundColor' => ['rgba(233, 213, 2, 0.7)', 'rgba(54, 162, 235, 0.7)'],
                ],
            ],
            'labels' => array_keys($orderTypeCounts),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}