<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .tot { font-weight: bold; }
  </style>
</head>
<body>
  <h3>{{ config('app.name') }} - Courtesy Receipt</h3>
  <p>Order #{{ $order->id }} — {{ $order->receipt->issued_at->format('Y-m-d H:i') }}</p>
  <table width="100%" cellspacing="0" cellpadding="3" border="1">
    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
    @foreach($order->items as $i)
      <tr>
        <td>{{ $i->product->name }}</td>
        <td align="center">{{ $i->quantity }}</td>
        <td align="right">{{ number_format($i->price,2) }}</td>
        <td align="right">{{ number_format($i->subtotal,2) }}</td>
      </tr>
    @endforeach
    <tr class="tot"><td colspan="3" align="right">TOTAL</td>
      <td align="right">{{ number_format($order->total,2) }}</td></tr>
  </table>
  <p>Payment: {{ strtoupper($order->receipt->payment_method->value) }}</p>
  <small>Non fiscale — courtesy receipt</small>
</body>
</html>
