<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import { useCartStore } from '@/stores/cart';
import { Minus, Plus, ShoppingCart, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue3-toastify';

interface Props {
    open: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const cartStore = useCartStore();
const isUpdating = ref<number | null>(null);

const open = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});

const incrementQuantity = async (
    productId: number,
    currentQuantity: number,
): Promise<void> => {
    if (isUpdating.value !== null) {
        return;
    }

    isUpdating.value = productId;
    try {
        await cartStore.updateQuantity(productId, currentQuantity + 1);
    } catch (error) {
        console.error('Failed to update quantity:', error);
    } finally {
        isUpdating.value = null;
    }
};

const decrementQuantity = async (
    productId: number,
    currentQuantity: number,
): Promise<void> => {
    if (isUpdating.value !== null) {
        return;
    }

    isUpdating.value = productId;
    try {
        await cartStore.updateQuantity(productId, currentQuantity - 1);
    } catch (error) {
        console.error('Failed to update quantity:', error);
    } finally {
        isUpdating.value = null;
    }
};

const removeItem = async (productId: number): Promise<void> => {
    if (isUpdating.value !== null) {
        return;
    }

    isUpdating.value = productId;
    try {
        await cartStore.removeItem(productId);
    } catch (error) {
        console.error('Failed to remove item:', error);
    } finally {
        isUpdating.value = null;
    }
};

const buyNow = (): void => {
    toast.success('Buy Now clicked');
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-w-2xl">
            <DialogHeader>
                <DialogTitle>Shopping Cart</DialogTitle>
                <DialogDescription>
                    Review your items before checkout
                </DialogDescription>
            </DialogHeader>

            <div
                v-if="cartStore.itemCount === 0"
                class="flex flex-col items-center justify-center py-12"
            >
                <ShoppingCart class="mb-4 h-16 w-16 text-muted-foreground" />
                <p class="text-lg font-medium text-muted-foreground">
                    Your cart is empty
                </p>
                <p class="text-sm text-muted-foreground">
                    Add some products to get started
                </p>
            </div>

            <div v-else class="max-h-[60vh] space-y-4 overflow-y-auto">
                <div
                    v-for="item in cartStore.items"
                    :key="item.id"
                    class="flex items-start gap-4 rounded-lg border p-4"
                >
                    <div
                        class="relative h-20 w-20 flex-shrink-0 overflow-hidden rounded-md"
                    >
                        <img
                            :src="item.product.image"
                            :alt="item.product.name"
                            class="h-full w-full object-cover"
                        />
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold">
                                    {{ item.product.name }}
                                </h4>
                                <p class="text-sm text-muted-foreground">
                                    ${{ item.product.price }} each
                                </p>
                            </div>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8"
                                :disabled="isUpdating === item.product_id"
                                @click="removeItem(item.product_id)"
                            >
                                <Trash2 class="h-4 w-4 text-destructive" />
                            </Button>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="h-8 w-8"
                                    :disabled="
                                        isUpdating === item.product_id ||
                                        item.quantity <= 1
                                    "
                                    @click="
                                        decrementQuantity(
                                            item.product_id,
                                            item.quantity,
                                        )
                                    "
                                >
                                    <Minus class="h-4 w-4" />
                                </Button>
                                <span class="w-8 text-center font-medium">{{
                                    item.quantity
                                }}</span>
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="h-8 w-8"
                                    :disabled="isUpdating === item.product_id"
                                    @click="
                                        incrementQuantity(
                                            item.product_id,
                                            item.quantity,
                                        )
                                    "
                                >
                                    <Plus class="h-4 w-4" />
                                </Button>
                            </div>
                            <p class="font-semibold">
                                ${{
                                    (
                                        item.product.price * item.quantity
                                    ).toFixed(2)
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <template v-if="cartStore.itemCount > 0">
                <Separator />
                <DialogFooter
                    class="flex-col items-center gap-2 sm:flex-row sm:justify-between"
                >
                    <div class="flex flex-col items-start">
                        <p class="text-sm text-muted-foreground">Total</p>
                        <p class="text-2xl font-bold">
                            ${{ cartStore.totalPrice.toFixed(2) }}
                        </p>
                    </div>
                    <Button size="lg" @click="buyNow">Buy Now</Button>
                </DialogFooter>
            </template>
        </DialogContent>
    </Dialog>
</template>
