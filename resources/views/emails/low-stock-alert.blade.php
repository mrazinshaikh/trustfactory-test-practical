<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alert</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            border-bottom: 3px solid #ef4444;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #ef4444;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .warning-icon {
            display: inline-block;
            width: 24px;
            height: 24px;
            background-color: #ef4444;
            color: #ffffff;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            font-weight: bold;
            margin-right: 10px;
        }
        .products-list {
            margin: 20px 0;
        }
        .product-item {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .product-item:first-child {
            margin-top: 0;
        }
        .product-item:last-child {
            margin-bottom: 0;
        }
        .product-name {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .stock-info {
            display: flex;
            flex-direction: column;
        }
        .stock-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .stock-quantity {
            font-size: 24px;
            font-weight: 700;
            color: #ef4444;
        }
        .message {
            background-color: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .message p {
            margin: 0;
            color: #92400e;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <span class="warning-icon">!</span>
                Low Stock Alert
            </h1>
        </div>

        <div class="products-list">
            @foreach($products as $product)
                <div class="product-item">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-details">
                        <div class="stock-info">
                            <div class="stock-label">Current Stock</div>
                            <div class="stock-quantity">{{ $product->stock_quantity }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="message">
            <p>
                <strong>Warning:</strong> {{ $products->count() === 1 ? 'This product is' : 'These products are' }} running low on stock. 
                Please consider restocking soon to avoid running out of inventory.
            </p>
        </div>

        <div class="footer">
            <p>This is an automated notification from your e-commerce system.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

