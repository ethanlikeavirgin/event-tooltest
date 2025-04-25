<template>
    <Head title="Welcome" />
    <div class="py-32">
        <Container class="bg-gray-100 !py-4 p-10 rounded-3xl">
            <h1 class="text-4xl font-bold">Purchase Page</h1>
            <div class="pb-8">This is the purchase page content inside the container.</div>
            {{ guesttoken }}
            <div class="relative">
                <!-- Dropdown -->
                <div class="fixed right-4 top-14 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg h-1/2 overflow-scroll">
                    <!-- Cart Items -->
                    <div class="p-4" v-for="item in cart">
                        <div class="flex items-center mb-4">
                            <img :src="`/storage/${item.items.file_path}`" class="w-12 h-12 rounded-md">
                            <div class="ml-4">
                                <h3 class="text-sm font-medium">{{ item.name }}</h3>
                                <p class="text-xs text-gray-500">{{ item.counter }} Ticket(s)</p>
                                <a @click.prevent="removecartitem(item.id)" class="text-xs text-red-500 cursor-pointer">Remove</a>
                            </div>
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="border-t border-gray-200 px-4 py-3">
                        <Link :href="`/cart`" class="btn btn--primary">
                            Checkout
                        </Link>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap -mr-6">
                <div class="w-1/3" v-for="item in items" :key="item">
                    <div class="mr-6 p-6 border border-black/20 rounded-3xl relative">
                        <Link :href="`/purchase/${item.id}`" class="absolute -top-4 -right-4 border bg-black/20 border-black/20 w-10 h-10 flex items-center justify-center rounded-full">
                            <span class="material-symbols-outlined text-black/80">north_east</span>
                        </Link>
                        <div v-if="item.file_path">
                            <img :src="`/storage/${item.file_path}`" alt="Module Image" class="h-40 w-full object-cover object-center rounded-3xl mb-4"/>
                        </div>
                        <div v-else>
                            No image available
                        </div>
                        <div>Product: {{ item.name }}</div>
                        <span>Price: {{ item.price }}</span>
                        {{ item.max }}
                        <form @submit.prevent="submit(item.id, item.name, item.price)" class="space-y-6">
                            <div class="flex flex-row gap-4">
                                <input v-model="counters[item.id]" id="counter" type="number" class="w-full main--input"/>
                                <button :disabled="form.processing" type="submit" class="btn btn--primary">Buy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Container>
    </div>
</template>
  
<script>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Container from '../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        items: Array,
        cart: Array,
        totalprice: Number,
        guesttoken: String,
    },
    components: {
        Container,
        AppLayout,
        Link,
        Head,
    },
    setup() {
        // Store prices uniquely for each item by their ID
        const counters = ref({});

        const form = useForm({
            price: '',
            item_id: '',
            name: '',
            counter: '',
        });

        const submit = (itemId, itemName, itemPrice) => {
            const counter = counters.value[itemId]; // Get the price for this item

            if (!counter) {
                console.error(`Price is required for item ${itemName}`);
                return;
            }

            form.item_id = itemId;
            form.name = itemName;
            form.price = itemPrice;
            form.counter = counter;

            console.log(`Submitting for item: ${itemName} with price: ${itemPrice} and ID: ${itemId}`);

            // Submit the form
            form.post(route('purchase.store'), {
                forceFormData: true,
                onFinish: () => {
                    console.log(`Purchase for ${itemName} was successful.`);
                },
            });
        };

        const removecartitem = (cartId) => {
            router.delete(route('purchase.destroy', { purchase: cartId }), {
                preserveScroll: true,
                onSuccess: () => console.log(`Item with ID ${cartId} removed from cart`),
                onError: (errors) => console.error(errors),
            });
        };


        return { counters, form, submit, removecartitem };
    },
};
</script>