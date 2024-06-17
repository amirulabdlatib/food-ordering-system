<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use Filament\Actions;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Customer\Resources\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $order = $this->getResource()
            ::getModel()
            ::create([
                'customer_id' => $data['customer_id'],
                'restaurant_id' => $data['restaurant_id'],
                'order_type' => $data['order_type'],
                'payment_method' => $data['payment_method'],
                'order_status' => $data['order_status'],
                'total_amount' => $data['total_amount'],
            ]);

        foreach ($data['menu_items'] as $menuItem) {
            $order->orderItems()->create([
                'menu_item_id' => $menuItem['menu_id'],
                'quantity' => $menuItem['quantity'],
            ]);
        }

        $restaurant = Restaurant::findOrFail($data['restaurant_id']);
        $sale = $restaurant->sales()->create([
            'total_sales' => $order->total_amount,
        ]);

        return $order;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Checkout')
                ->color('primary')
                ->extraAttributes(['style' => 'width: 100%;']),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_id'] = auth()->id();
        return $data;
    }
}