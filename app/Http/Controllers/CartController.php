<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Create or update a cart item.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem = CartItem::query()
            ->updateOrCreate(
                [
                    'user_id'    => $request->user()->id,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $request->quantity,
                ]
            );

        return response()->json([
            'cart_item' => $cartItem->loadMissing('product'),
        ]);
    }

    /**
     * Remove the specified cart item.
     */
    public function destroy(Request $request, Product $product)
    {
        $cartItem = CartItem::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
