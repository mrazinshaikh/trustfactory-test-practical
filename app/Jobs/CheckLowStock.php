<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Product;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Notifications\LowStockNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class CheckLowStock implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $productIds,
    ) {
        //
    }

    public function handle(): void
    {
        $products = Product::query()
            ->whereIn('id', $this->productIds)
            ->where('stock_quantity', '<', 10)
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        $admin = User::where('is_admin', true)->first();

        if (! $admin) {
            return;
        }

        Notification::send($admin, new LowStockNotification($products));
    }
}
