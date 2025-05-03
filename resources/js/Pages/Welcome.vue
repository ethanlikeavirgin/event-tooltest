<template>
    <Head title="Welcome" />
    <FrontendLayout></FrontendLayout>
        <section class="min-h-screen bg-cover bg-center w-full h-full flex items-center hero relative" style="background-image: url('storage/files/1LNg0Sum6rv0fhoKVCFnhqScQuf2Bbrz7fZO1wh7.png')">
            <Container>
                <div class="relative z-10">
                    <h1>
                        <span class="font-bold">The smart way</span><br>
                        <span class="font-bold">to ticket</span><br>
                        <span class="font-bold">your</span> event
                    </h1>
                </div>
                <div class="absolute z-10 bottom-20 max-w-6xl mx-auto w-full flex flex-row">
                    <a class="btn btn-large btn--primary mr-6">Bekijk de events</a>
                    <a class="btn btn-large btn--white">Contacteer ons</a>
                </div>
                <div class="fixed right-12 bottom-20 bg-white rounded-full w-16 h-16 z-50 flex items-center justify-center">
                    <span class="material-symbols-outlined">local_activity</span>
                </div>
            </Container>
        </section>
        <section class="py-32">
            <Container>
                <h2 class="text-center font-bold pb-24">Checkout our <span class="text-primary">prices</span></h2>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-4">
                        <div class="main-card primary">
                            <span>Starter</span>
                            <div class="flex flex-row gap-2 items-end pt-4">
                                <h2>€ 25</h2>
                                <span>/month</span>
                            </div>
                            <a class="btn btn--white w-full mt-8 text-center !rounded-xl">Order now</a>
                            <div class="mt-8">
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Unlimited events</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Unlimited tickets</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Your own team</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>24/7 support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-4">
                        <div class="main-card secondary">
                            <span>Starter</span>
                            <div class="flex flex-row gap-2 items-end pt-4">
                                <h2>€ 35</h2>
                                <span>/month</span>
                            </div>
                            <a class="btn btn--white w-full mt-8 text-center !rounded-xl">Order now</a>
                            <div class="mt-8">
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Unlimited events</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Unlimited tickets</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Your own team</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>24/7 support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-4">
                        <div class="main-card primary">
                            <span>Starter</span>
                            <div class="flex flex-row gap-2 items-end pt-4">
                                <h2>€ 150</h2>
                                <span>/once</span>
                            </div>
                            <a class="btn btn--white w-full mt-8 text-center !rounded-xl">Order now</a>
                            <div class="mt-8">
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Unlimited events</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Unlimited tickets</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>Your own team</div>
                                </div>
                                <div class="flex flex-row gap-4">
                                    <Checkicon></Checkicon>
                                    <div>24/7 support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </section>
        <section class="bg-cover bg-center bg-repeat-y w-full h-full hero alt relative" style="background-image: url('storage/files/1LNg0Sum6rv0fhoKVCFnhqScQuf2Bbrz7fZO1wh7.png')">
            <div class="py-32">
                <div class="relative z-10">
                <Container>
                    <h2 class="text-black font-bold pb-24">Current events</h2>
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-4" v-for="item in items">
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

        <div class="py-32">
            <Container class="bg-gray-100 !py-4 p-10 rounded-3xl">
                <h1 class="text-4xl font-bold">Purchase Page</h1>
                <div class="pb-8">This is the purchase page content inside the container.</div>
                {{ guesttoken }}
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
</template>
  
<script>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Container from '../Components/Container.vue';
import Logo from '../Components/Logo.vue';
import Checkicon from '../Components/Checkicon.vue';
import Cart from '../Components/Cart.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

export default {
    props: {
        items: Array,
        cart: Array,
        totalprice: Number,
        guesttoken: String,
    },
    components: {
        Container,
        Logo,
        Checkicon,
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