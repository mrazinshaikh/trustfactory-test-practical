import type { CartItem, Product } from '@/types';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { toast } from 'vue3-toastify';

async function apiRequest<T>(
    url: string,
    options: RequestInit = {},
): Promise<T> {
    const headers: Record<string, string> = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    };

    if (options.headers) {
        if (options.headers instanceof Headers) {
            options.headers.forEach((value, key) => {
                headers[key] = value;
            });
        } else if (Array.isArray(options.headers)) {
            options.headers.forEach(([key, value]) => {
                headers[key] = value;
            });
        } else {
            Object.assign(headers, options.headers);
        }
    }

    const response = await fetch(url, {
        ...options,
        headers,
        credentials: 'same-origin',
    });

    if (!response.ok) {
        const error = await response.json().catch(() => ({
            message: 'Request failed',
        }));
        throw new Error(
            error.message || `HTTP error! status: ${response.status}`,
        );
    }

    return response.json();
}

export const useCartStore = defineStore('cart', () => {
    const items = ref<CartItem[]>([]);
    const isSyncing = ref(false);

    function initializeFromProps(cartItems: CartItem[]): void {
        items.value = cartItems;
    }

    const totalItems = computed(() => {
        return items.value.reduce((total, item) => total + item.quantity, 0);
    });

    const totalPrice = computed(() => {
        return items.value.reduce((total, item) => {
            return total + item.product.price * item.quantity;
        }, 0);
    });

    const itemCount = computed(() => {
        return items.value.length;
    });

    const getItemByProductId = (productId: number): CartItem | undefined => {
        return items.value.find((item) => item.product_id === productId);
    };

    async function addItem(
        product: Product,
        quantity: number = 1,
    ): Promise<void> {
        const existingItem = items.value.find(
            (item) => item.product_id === product.id,
        );

        if (existingItem) {
            await updateQuantity(product.id, existingItem.quantity + quantity);
            return;
        }

        const tempItem: CartItem = {
            id: 0,
            user_id: 0,
            product_id: product.id,
            quantity,
            product,
            created_at: '',
            updated_at: '',
        };
        items.value.push(tempItem);

        isSyncing.value = true;
        try {
            const response = await apiRequest<{ cart_item: CartItem }>(
                `/api/cart/${product.id}`,
                {
                    method: 'PUT',
                    body: JSON.stringify({
                        product_id: product.id,
                        quantity,
                    }),
                },
            );

            const index = items.value.findIndex(
                (item) => item.product_id === product.id && item.id === 0,
            );
            if (index !== -1) {
                items.value[index] = response.cart_item;
            } else {
                items.value.push(response.cart_item);
            }

            toast.success('Item added to cart successfully!');
        } catch (error) {
            const index = items.value.findIndex(
                (item) => item.product_id === product.id,
            );
            if (index !== -1) {
                items.value.splice(index, 1);
            }
            console.error('Failed to add item to cart:', error);
            toast.error('Failed to add item to cart');
            throw error;
        } finally {
            isSyncing.value = false;
        }
    }

    async function updateQuantity(
        productId: number,
        quantity: number,
    ): Promise<void> {
        if (quantity <= 0) {
            await removeItem(productId);
            return;
        }

        const item = items.value.find((item) => item.product_id === productId);
        if (!item) {
            return;
        }

        const oldQuantity = item.quantity;
        item.quantity = quantity;

        isSyncing.value = true;
        try {
            const response = await apiRequest<{ cart_item: CartItem }>(
                `/api/cart/${productId}`,
                {
                    method: 'PUT',
                    body: JSON.stringify({
                        product_id: productId,
                        quantity,
                    }),
                },
            );

            const itemIndex = items.value.findIndex(
                (i) => i.product_id === productId,
            );
            if (itemIndex !== -1) {
                items.value[itemIndex] = response.cart_item;
            }

            toast.success('Cart item updated successfully!');
        } catch (error) {
            item.quantity = oldQuantity;
            console.error('Failed to update cart item:', error);
            toast.error('Failed to update cart item');
            throw error;
        } finally {
            isSyncing.value = false;
        }
    }

    async function removeItem(productId: number): Promise<void> {
        const item = items.value.find((item) => item.product_id === productId);
        if (!item) {
            return;
        }

        const itemToRemove = item;
        const index = items.value.findIndex((i) => i.id === item.id);
        items.value.splice(index, 1);

        isSyncing.value = true;
        try {
            await apiRequest<{ success: boolean }>(`/api/cart/${productId}`, {
                method: 'DELETE',
            });

            toast.success('Cart item removed successfully!');
        } catch (error) {
            items.value.splice(index, 0, itemToRemove);
            console.error('Failed to remove cart item:', error);
            toast.error('Failed to remove cart item');
            throw error;
        } finally {
            isSyncing.value = false;
        }
    }

    async function clearCart(): Promise<void> {
        const itemsToRestore = [...items.value];
        items.value = [];

        isSyncing.value = true;
        try {
            await Promise.all(
                itemsToRestore.map((item) =>
                    apiRequest<{ success: boolean }>(
                        `/api/cart/${item.product_id}`,
                        {
                            method: 'DELETE',
                        },
                    ),
                ),
            );

            toast.success('Cart cleared successfully!');
        } catch (error) {
            items.value = itemsToRestore;
            console.error('Failed to clear cart:', error);
            toast.error('Failed to clear cart');
            throw error;
        } finally {
            isSyncing.value = false;
        }
    }

    return {
        items,
        isSyncing,
        totalItems,
        totalPrice,
        itemCount,
        getItemByProductId,
        initializeFromProps,
        addItem,
        updateQuantity,
        removeItem,
        clearCart,
    };
});
