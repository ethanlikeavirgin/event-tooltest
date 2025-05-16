<template>
    <Head title="Welcome" />
    <FrontendLayout :auth="auth"></FrontendLayout>
        <section class="min-h-screen bg-cover bg-center w-full h-full flex items-center hero relative" style="background-image: url('storage/files/1LNg0Sum6rv0fhoKVCFnhqScQuf2Bbrz7fZO1wh7.png')">
            <Container>
                <div class="relative z-10">
                    <h1>
                        <span class="font-bold">The smart way</span><br>
                        <span class="font-bold">to ticket</span><br>
                        <span class="font-bold">your</span> event
                    </h1>
                </div>
                <div class="absolute z-10 bottom-20 max-w-6xl mx-auto sm:w-full w-fit flex md:flex-row flex-col md:gap-0 gap-4">
                    <a class="btn btn-large btn--primary w-fit mr-6">Bekijk de events</a>
                    <a class="btn btn-large btn--white w-fit">Contacteer ons</a>
                </div>
                <div class="fixed sm:right-5 right-5 sm:bottom-20 bottom-6 shadow-black/20 shadow-lg bg-white rounded-full w-16 h-16 z-50 flex items-center justify-center">
                    <span class="material-symbols-outlined">local_activity</span>
                </div>
            </Container>
        </section>
        <Plans :plans="plans"></Plans>
        <section class="bg-cover bg-center bg-repeat-y w-full h-full hero alt relative" style="background-image: url('storage/files/1LNg0Sum6rv0fhoKVCFnhqScQuf2Bbrz7fZO1wh7.png')">
            <div class="py-32">
                <div class="relative z-10">
                <Container>
                    <h2 class="text-black font-bold pb-24">Current events</h2>
                    <div class="grid grid-cols-12 gap-6">
                        <div class="lg:col-span-4 md:col-span-6 col-span-12" v-for="item in items">
                            <div class="main-card third">
                                <span>{{ item.name }}</span>
                                <div class="flex flex-row gap-2 items-end pt-4">
                                    <h2>€ {{ item.price }}</h2>
                                </div>
                                <div class="my-4">
                                    <img :src="'/storage/' + item.file_path" class="h-40 w-full object-cover object-center rounded-[35px]" />
                                </div>
                                <div class="mb-4">
                                    {{ item.description }}
                                </div>
                                <form @submit.prevent="submit(item.id, item.name, item.price)" class="space-y-6">
                                    <div class="flex flex-row justify-end ml-auto gap-4">
                                        <input placeholder="Aantal" v-model="counters[item.id]" id="counter" type="number" class="main--input counter"/>
                                        <button :disabled="form.processing" type="submit" class="btn btn--primary">Buy</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </Container>
                </div>
            </div>
        </section>
        <Cart :cart="cart"></Cart>
</template>
  
<script>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Container from '../Components/Container.vue';
import Logo from '../Components/Logo.vue';
import Checkicon from '../Components/Checkicon.vue';
import Cart from '../Components/Cart.vue';
import Plans from '../Components/Plans.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

export default {
    props: {
        plans: Array,
        items: Array,
        cart: Array,
        totalprice: Number,
        guesttoken: String,
        auth: Object,
    },
    components: {
        Container,
        Logo,
        Checkicon,
        Plans,
        Cart,
        AppLayout,
        FrontendLayout,
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
                preserveScroll: true,
                preserveState: true,
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