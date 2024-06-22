<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //
    public function show(Order $order){
        return view('payment', compact('order'));
    }

    public function checkout(Order $order)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // Create a new Price object
        $price = \Stripe\Price::create([
            'product_data' => [
                'name' => "Order #{$order->id} from {$order->restaurant->name}",
            ],
            'unit_amount' => $order->total_amount * 100, // Amount in cents
            'currency' => 'myr',
        ]);

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price' => $price->id,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' =>  route('success', ['order' => $order->id]),
            'cancel_url' => route('cancel', ['order' => $order->id]),
        ]);

        return redirect()->away($checkout_session->url);
    }

    public function success(Request $request,Order $order)
    {
        $order->update(['order_status' => 'paid']);

        return redirect('/customer');
    }

    public function cancel(Order $order)
    {
        return view('cancel', compact('order'));
    }
}