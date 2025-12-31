<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useCartStore } from '@/stores/cart';
import { type Product } from '@/types';
import { ShoppingCart } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue3-toastify';

const props = defineProps<{
    product: Product;
}>();

const cartStore = useCartStore();
const isAdding = ref(false);

const cartItem = computed(() => {
    return cartStore.getItemByProductId(props.product.id);
});

const buttonText = computed(() => {
    if (cartItem.value) {
        return `${cartItem.value.quantity} product${cartItem.value.quantity > 1 ? 's' : ''} added to cart`;
    }
    return isAdding.value ? 'Adding...' : 'Add to Cart';
});

const addToCart = async (): Promise<void> => {
    if (props.product.stock_quantity === 0) {
        toast.error('This product is out of stock');
        return;
    }

    if (isAdding.value) {
        return;
    }

    const cartItem = cartStore.getItemByProductId(props.product.id);
    const currentQuantity = cartItem ? cartItem.quantity : 0;
    const newQuantity = currentQuantity + 1;

    if (props.product.stock_quantity < newQuantity) {
        toast.error(
            `Cannot add more items. Available stock: ${props.product.stock_quantity}, Already in cart: ${currentQuantity}`,
        );
        return;
    }

    isAdding.value = true;
    try {
        await cartStore.addItem(props.product, 1);
    } catch (error) {
        console.error('Failed to add item to cart:', error);
    } finally {
        isAdding.value = false;
    }
};
</script>

<template>
    <Card class="flex flex-col overflow-hidden">
        <div class="relative aspect-square w-full overflow-hidden">
            <img
                :src="product.image"
                :alt="product.name"
                class="h-full w-full object-cover"
            />
        </div>
        <CardHeader>
            <CardTitle class="line-clamp-2">{{ product.name }}</CardTitle>
        </CardHeader>
        <CardContent class="mt-auto flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-lg font-semibold">${{ product.price }}</span>
                <span
                    class="text-sm text-muted-foreground"
                    :class="{
                        'text-green-600 dark:text-green-400':
                            product.stock_quantity > 0,
                        'text-red-600 dark:text-red-400':
                            product.stock_quantity === 0,
                    }"
                >
                    {{
                        product.stock_quantity > 0 ? 'In Stock' : 'Out of Stock'
                    }}
                </span>
            </div>
            <Button
                :disabled="product.stock_quantity === 0 || isAdding"
                @click="addToCart"
                class="w-full"
            >
                <ShoppingCart class="mr-2 h-4 w-4" />
                {{ buttonText }}
            </Button>
        </CardContent>
    </Card>
</template>
