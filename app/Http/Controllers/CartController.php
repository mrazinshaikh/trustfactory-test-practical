<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use App\Jobs\CheckLowStock;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Create or update a cart item.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->user()->pendingCart();

        $cartItem = CartItem::query()
            ->updateOrCreate(
                [
                    'cart_id'    => $cart->id,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $request->quantity,
                ]
            );

        return response()->json([
            'cart_item' => $cartItem->loadMissing(['product', 'cart']),
        ]);
    }

    /**
     * Remove the specified cart item.
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $cart = $request->user()->pendingCart();

        $cartItem = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Complete the purchase (Buy Now).
     */
    public function buyNow(Request $request): JsonResponse
    {
        $cart = $request->user()->pendingCart();

        $cartItems = $cart->items()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty',
            ], 422);
        }

        // Validate stock availability
        $stockErrors = [];
        foreach ($cartItems as $item) {
            if ($item->product->stock_quantity < $item->quantity) {
                $stockErrors[] = "Insufficient stock for {$item->product->name}. Available: {$item->product->stock_quantity}, Requested: {$item->quantity}";
            }
        }

        if (! empty($stockErrors)) {
            throw ValidationException::withMessages([
                'stock' => $stockErrors,
            ]);
        }

        // Process purchase in transaction
        DB::transaction(function () use ($cart, $cartItems) {
            foreach ($cartItems as $item) {
                $item->product->decrementStock($item->quantity);
            }

            $cart->update(['status' => Cart::COMPLETED]);
        });

        $productIds = $cartItems->pluck('product_id')->toArray();
        dispatch(new CheckLowStock($productIds));

        return response()->json([
            'success' => true,
            'message' => 'Purchase completed successfully',
        ]);
    }
}
