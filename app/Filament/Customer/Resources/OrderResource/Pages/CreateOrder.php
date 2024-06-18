<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use Filament\Actions;
use Stripe\StripeClient;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Customer\Resources\OrderResource;
use App\Models\LoyaltyPoint;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
        // return "Hello World"; => http://127.0.0.1:8001/customer/orders/Hello%20World
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
                'order_status' => $data['order_status'], //payment pending
                'total_amount' => $data['total_amount'],
            ]);

        // Create Stripe session after order creation
        $this->createStripeSession($order);

        foreach ($data['menu_items'] as $menuItem) {
            $order->orderItems()->create([
                'menu_item_id' => $menuItem['menu_id'],
                'quantity' => $menuItem['quantity'],
            ]);
        }

        $restaurant = Restaurant::findOrFail($data['restaurant_id']);
        $restaurant->sales()->create([
            'total_sales' => $order->total_amount,
        ]);

        $points = floor($data['total_amount']);
        $points_casted = (int) $points;

        // Find the existing LoyaltyPoint record for the customer or create a new one
        $loyalty_point = LoyaltyPoint::firstOrNew(['customer_id' => auth()->user()->id]);

        // Update the points_earned by adding the new points
        $loyalty_point->points_earned += $points_casted;

        // if does loyalty point for the customer is not exist yet, set the loyalty point to 0
        if (!$loyalty_point->exists) {
            $loyalty_point->points_redeemed = 0;
        }

        $loyalty_point->save();

        return $order;
    }

    protected function createStripeSession($order)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        $session = $stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'myr',
                        'product_data' => [
                            'name' => 'Send some money!!!',
                        ],
                        'unit_amount' => 500,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => url('/customer/orders'),
            'cancel_url' => url('/customer/orders'),
        ]);

        // Redirect to Stripe session URL
        return redirect($session->url);
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