<template>  
    <a @click="toggleCart" class="fixed top-5 right-5 bg-white shadow-lg shadow-black/20 h-16 w-16 rounded-full flex items-center justify-center cursor-pointer">
        <span class="material-symbols-outlined">shopping_basket</span>
        <span v-if="totalTickets > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
            {{ totalTickets }}
        </span>
    </a>
    <Transition name="fade-slide">
        <div v-if="showCart && Array.isArray(cart) && cart.length > 0" class="fixed right-4 top-20 mt-2 w-72 h-fit bg-white border border-gray-200 rounded-[35px] shadow-lg overflow-scroll z-[999]">
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
    </Transition>
</template>
<script>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

export default {
    props: {
        cart: Array,
        items: Array,
        totalprice: Number,
        guesttoken: String,
    },
    components: {
        Link,
        Head,
    },
    setup(props) {
        // Store prices uniquely for each item by their ID
        const showCart = ref(false);
        const toggleCart = () => {
            showCart.value = !showCart.value;
        };
        const totalTickets = computed(() => {
            return props.cart?.reduce((sum, item) => sum + Number(item.counter || 0), 0);
        });
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


        return { showCart, toggleCart, counters, form, submit, removecartitem, totalTickets };
    },
};
</script>
<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.3s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>