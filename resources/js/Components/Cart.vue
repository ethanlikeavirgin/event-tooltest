<template>  
    <div v-if="cart.length > 0" class="fixed right-4 top-14 mt-2 w-72 h-fit bg-white border border-gray-200 rounded-[35px] shadow-lg overflow-scroll">
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
        <div class="border-t border-gray-200 px-4 py-3">
            <Link :href="`/cart`" class="btn btn--primary w-fit">
                Checkout
            </Link>
        </div>
    </div>
</template>
<script>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

export default {
    props: {
        items: Array,
        cart: Array,
        totalprice: Number,
        guesttoken: String,
    },
    components: {
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