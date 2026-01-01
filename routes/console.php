<?php

use App\Models\Product;
use App\Jobs\CheckLowStock;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('check:low-stock', function () {
    $products = Product::where('stock_quantity', '<', 10)->get();

    if ($products->isEmpty()) {
        $products = Product::limit(2)->get();
        $products->toQuery()
            ->update([
                'stock_quantity' => rand(5, 9),
            ]);
    }

    $productIds = $products->pluck('id')->toArray();

    dispatch_sync(new CheckLowStock($productIds));
})->purpose('Check low stock');

Schedule::command('sales:report-daily')
    ->dailyAt('18:00');
