<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h2>Thank you for your order!</h2>
    <p>Order ID: {{ $order->id }}</p>
    <p>Total Amount: {{ $order->total_amount }}</p>
    <p>We will process your order soon.</p>
</body>
</html>
