<template>
    <AppLayout title="Source">
        <div class="py-32">
            <Container class="bg-gray-100 !py-4 p-10 rounded-3xl">
                <h1 class="text-4xl font-bold">Source Page</h1>
                <div class="py-8">This is the source page content inside the container</div>
                <h2 class="font-bold text-2xl mb-6">Your items</h2>
                <div class="border border-black/20 rounded-[30px] p-6 mb-2">
                    <div class="font-medium text-xl pb-4">{{ item.name }}</div>
                    <div class="flex flex-col">
                        <div>
                            <span class="underline text-lg">All Sold:</span>
                            <div v-for="sell in sells" :key="sell.order_id">
                                Order #{{ sell.order_id }} {{ sell.email }}<br>
                                {{ sell.item.item_id }} - {{ sell.item.name }} - {{ sell.item.counter }} x €{{ sell.item.price }} = €{{ sell.item.line_total }}
                            </div>
                        </div>
                        <div>
                            <span class="underline text-lg">In Cart:</span>
                            <div v-for="cartitem in carts">
                                {{ cartitem.counter }} items in cart by user {{ cartitem.user.name }} - {{ cartitem.user.email }}
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </div>
    </AppLayout>
</template>
  
<script>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Container from '../../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        item: Object,
        carts: Array,
        sells: Array,
    },
    components: {
        Container,
        AppLayout,
    },
};
</script>