<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
</head>
<body>
    <h1>Payment Successful!</h1>
    <p>Your order (ID: {{ $order->id }}) has been successfully paid.</p>
    <p>Current order status: {{ $order->order_status }}</p>
</body>
</html>