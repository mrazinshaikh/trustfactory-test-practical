<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    protected $fillable = [
        'name',
        'image',
        'price',
        'stock_quantity',
    ];

    public function decrementStock(int $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            throw ValidationException::withMessages([
                'stock' => "Insufficient stock. Available: {$this->stock_quantity}, Requested: {$quantity}",
            ]);
        }

        $this->decrement('stock_quantity', $quantity);

        return true;
    }
}
