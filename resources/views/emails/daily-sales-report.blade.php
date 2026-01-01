<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
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
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .date {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }
        .summary {
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .summary-label {
            color: #6b7280;
            font-size: 14px;
        }
        .summary-value {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
        }
        .message {
            color: #4b5563;
            font-size: 14px;
            margin: 20px 0;
            padding: 15px;
            background-color: #f0f9ff;
            border-left: 3px solid #3b82f6;
            border-radius: 4px;
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
            <h1>Daily Sales Report</h1>
            <div class="date">{{ $reportDate->format('F j, Y') }}</div>
        </div>

        <div class="summary">
            <div class="summary-item">
                <span class="summary-label">Total Orders</span>
                <span class="summary-value">{{ $summary['total_orders'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Items Sold</span>
                <span class="summary-value">{{ number_format($summary['total_items_sold']) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Revenue</span>
                <span class="summary-value">${{ number_format($summary['total_revenue'], 2) }}</span>
            </div>
        </div>

        @if(empty($salesData))
            <div class="message" style="background-color: #fef3c7; border-left-color: #f59e0b;">
                <p style="margin: 0; color: #92400e; font-weight: 500;">
                    No sales were recorded today.
                </p>
            </div>
        @else
            <div class="message">
                <p>
                    A detailed CSV report with all product sales for this day is attached to this email.
                </p>
            </div>
        @endif

        <div class="footer">
            <p>This is an automated daily sales report from your e-commerce system.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

