<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmed — {{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; color: #18181b; -webkit-font-smoothing: antialiased; }
        .wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px 40px; }
        .card { background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #b45309 0%, #d97706 100%); padding: 40px 40px 32px; text-align: center; }
        .logo-text { font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; margin-bottom: 16px; }
        .header-title { font-size: 26px; font-weight: 800; color: #ffffff; line-height: 1.2; margin-bottom: 8px; }
        .header-sub { font-size: 14px; color: rgba(255,255,255,0.85); }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; color: #3f3f46; margin-bottom: 16px; }
        .message { font-size: 15px; color: #52525b; line-height: 1.7; margin-bottom: 24px; }
        .order-info { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .order-info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; color: #52525b; }
        .order-info-row strong { color: #18181b; }
        .items-label { font-size: 13px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; color: #a1a1aa; margin-bottom: 12px; }
        .item-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: #3f3f46; border-bottom: 1px solid #f4f4f5; }
        .item-row:last-child { border-bottom: none; }
        .total-row { display: flex; justify-content: space-between; padding: 12px 0 0; font-size: 16px; font-weight: 800; color: #b45309; border-top: 2px solid #fcd34d; margin-top: 8px; }
        .footer { padding: 24px 40px; background: #fafafa; border-top: 1px solid #f4f4f5; text-align: center; }
        .footer-brand { font-size: 13px; font-weight: 700; color: #b45309; letter-spacing: 0.5px; margin-bottom: 6px; }
        .footer-text { font-size: 12px; color: #a1a1aa; line-height: 1.6; }
        @media only screen and (max-width: 480px) {
            .wrapper { margin: 0 auto; padding: 0 8px 24px; }
            .header { padding: 28px 20px 24px; }
            .header-title { font-size: 22px; }
            .body { padding: 24px 20px; }
            .footer { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="logo-text">{{ config('app.name') }}</div>
                <div class="header-title">Order Confirmed!</div>
                <div class="header-sub">Thank you for your order</div>
            </div>

            <div class="body">
                <p class="greeting">Hi, <strong>{{ $order->user->name }}</strong>!</p>
                <p class="message">
                    Your order has been placed successfully. We're preparing your delicious baked goods!
                </p>

                <div class="order-info">
                    <div class="order-info-row">
                        <span>Order Number</span>
                        <strong>#{{ $order->id }}</strong>
                    </div>
                    <div class="order-info-row">
                        <span>Order Type</span>
                        <strong>{{ $order->type->label() }}</strong>
                    </div>
                    @if($order->delivery_address)
                    <div class="order-info-row">
                        <span>Delivery Address</span>
                        <strong>{{ $order->delivery_address }}</strong>
                    </div>
                    @endif
                    @if($order->notes)
                    <div class="order-info-row">
                        <span>Notes</span>
                        <strong>{{ $order->notes }}</strong>
                    </div>
                    @endif
                </div>

                <div class="items-label">Order Items</div>

                @foreach($order->items as $item)
                <div class="item-row">
                    <span>{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                    <strong>₱{{ number_format($item->subtotal, 2) }}</strong>
                </div>
                @endforeach

                <div class="total-row">
                    <span>Total</span>
                    <span>₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <div class="footer">
                <div class="footer-brand">{{ config('app.name') }}</div>
                <div class="footer-text">
                    This is an automated message — please do not reply to this email.
                </div>
            </div>
        </div>

        <p style="text-align:center; font-size:12px; color:#a1a1aa; margin-top:20px;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
