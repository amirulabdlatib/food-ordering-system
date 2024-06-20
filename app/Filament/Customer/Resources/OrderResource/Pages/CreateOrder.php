<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use Filament\Actions;
use Stripe\StripeClient;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Customer\Resources\OrderResource;
use App\Models\LoyaltyPoint;
use GuzzleHttp\Client;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        // dd($this->getResource()::getUrl('index'));
        return $this->getResource()::getUrl('index');
    }

    protected function customer_checkout(array $data)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        // Create a new Price object
        $price = $stripe->prices->create([
            'product_data' => [
                'name' => 'Product Name',
            ],
            'unit_amount' => 10000, // Amount in cents
            'currency' => 'myr',
        ]);

        // Create a new Checkout Session
        $session = $stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price' => $price->id,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('filament.customer.resources.orders.index'),
            'cancel_url' => route('filament.customer.resources.orders.index'),
            'client_reference_id' => $data['customer_id'],
        ]);

        $checkoutSessionUrl = "https://checkout.stripe.com/c/pay/{$session->id}";
        dd($checkoutSessionUrl);

        return redirect($checkoutSessionUrl);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_id'] = auth()->id();

        // Call the checkout method
        $this->customer_checkout($data);

        return $data;
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

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Checkout')
                ->color('primary')
                ->extraAttributes(['style' => 'width: 100%;']),
        ];
    }
}