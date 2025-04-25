<template>
    <div class="py-32">
        <Container class="bg-gray-100 !py-4 p-10 rounded-3xl">
            <h1 class="text-4xl font-bold">Purchase Page</h1>
            <div class="pb-8">This is the purchase page content inside the container.</div>

            <!-- Loop through products -->
            <div v-for="product in detailitems" :key="product.id" class="mb-12 border-b pb-6">
                <div v-if="product.file_path">
                    <img :src="`/storage/${product.file_path}`" class="w-full object-cover object-center h-72 rounded-2xl">
                </div>
                <div v-else>No image available</div>

                <div>Product: {{ product.name }}</div>
                <span>Price: {{ product.price }}</span>
                <span>Max: {{ product.max }}</span>

                <form @submit.prevent="submit(product.id, product.name, product.price)" class="space-y-6 mt-4">
                    <div class="flex flex-row gap-4">
                        <input
                            v-model="counters[product.id]"
                            type="number"
                            min="1"
                            :max="product.max"
                            class="w-full main--input"
                        />
                        <button :disabled="form.processing" type="submit" class="btn btn--primary">Buy</button>
                    </div>
                </form>
            </div>

            <!-- Cart Items -->
            <h2 class="text-2xl font-bold mt-12">Cart</h2>
            <div v-for="item in detailitems" :key="item.id" class="grid grid-cols-12 mt-4">
                <div class="col-span-4 mr-6 p-6 border border-black/20 rounded-3xl relative">
                    {{ item.name }}
                    <button @click="removecartitem(item.id)" class="absolute top-4 right-4 text-red-500">Remove</button>
                </div>
            </div>
        </Container>
    </div>
</template>
  
<script>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Container from '../../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        items: Array, // instead of a single 'item'
        detailitems: Array,
    },
    components: {
        Container,
        AppLayout,
    },
    setup() {
        const counters = ref({});

        const form = useForm({
            price: '',
            item_id: '',
            name: '',
            counter: '',
        });

        const submit = (itemId, itemName, itemPrice) => {
            const counter = counters.value[itemId];

            if (!counter || counter <= 0) {
                console.error(`Please enter quantity for ${itemName}`);
                return;
            }

            form.item_id = itemId;
            form.name = itemName;
            form.price = itemPrice;
            form.counter = counter;

            form.post(route('purchase.store'), {
                forceFormData: true,
                onFinish: () => {
                    counters.value[itemId] = ''; // reset input after successful add
                },
            });
        };

        const removecartitem = (cartId) => {
            router.delete(route('purchase.destroy', { purchase: cartId }), {
                preserveScroll: true,
            });
        };

        return { counters, form, submit, removecartitem };
    },
};
</script>