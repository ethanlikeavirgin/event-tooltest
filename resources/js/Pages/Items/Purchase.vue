<template>
    <!--Add FrontenLayout here-->
    <AppLayout title="Purchase">
        <div class="py-32">
            <Container class="bg-gray-100 !py-4 p-10 rounded-3xl">
                <h1 class="text-4xl font-bold">Purchase Page</h1>
                <div class="pb-8">This is the purchase page content inside the container.</div>
                <div class="relative">
                    <!-- Dropdown -->
                    <Cart :cart="cart"></Cart>
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
    </AppLayout>
</template>
  
<script>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Container from '../../Components/Container.vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Cart from '../../Components/Cart.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        items: Array,
        cart: Array,
        totalprice: Number,
    },
    components: {
        Container,
        Cart,
        AppLayout,
        Link,
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