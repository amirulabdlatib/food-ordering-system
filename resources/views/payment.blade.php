<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    payment page
    <form action="{{ route('checkout', $order->id) }}" method="post">
        @csrf
        Order Id: {{ $order->id }} <br>
        From Restaurant Name: {{ $order->restaurant->name }}<br>
        Total Amout: RM{{ $order->total_amount }}<br>
        <button>Checkout</button>
    </form>
</body>
</html>