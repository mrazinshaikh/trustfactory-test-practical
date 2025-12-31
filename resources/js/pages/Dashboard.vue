<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ProductCard from '@/components/ProductCard.vue';
import { dashboard } from '@/routes';
import { AppPageProps, type BreadcrumbItem, type Product } from '@/types';
import { Head, InfiniteScroll, usePage } from '@inertiajs/vue3';

const page = usePage<AppPageProps<{ products: { data: Product[] } }>>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <InfiniteScroll data="products" :preserve-url="true">
                <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                    <ProductCard
                        v-for="product in page.props.products.data"
                        :key="product.id"
                        :product="product"
                    />
                </div>
            </InfiniteScroll>
        </div>
    </AppLayout>
</template>
