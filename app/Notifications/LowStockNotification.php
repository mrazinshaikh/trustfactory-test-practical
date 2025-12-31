<?php

namespace App\Notifications;

use App\Models\Product;
use App\Mail\LowStockAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

// use Illuminate\Contracts\Queue\ShouldQueue;

class LowStockNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product>  $products
     */
    public function __construct(
        public Collection $products,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\User  $notifiable
     */
    public function toMail(object $notifiable): LowStockAlert
    {
        return (
            new LowStockAlert($this->products)
        )->to($notifiable->email, $notifiable->name);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'products' => $this->products->map(fn (Product $product) => [
                'product_id'     => $product->id,
                'product_name'   => $product->name,
                'stock_quantity' => $product->stock_quantity,
            ])->toArray(),
        ];
    }
}
