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
        <section>
            <div class="hidden opacity-O comment-items">
                1. @csrf onder formulier
                2. <form action="{{ route('posts.create') }}" method="POST" enctype="multipart/form-data"></form> -> Image submitting
                <3. php artisan storage:link -> voor images te tonen in map + map aanmaken > storage/app/public/images
                4. <img src="{{ asset('storage/' . $post->image) }}" width="100" /> -> images tonen
                5. tailwind in head -> @vite(['resources/css/app.css', 'resources/js/app.js'])

                composer create-project laravel/laravel example-app
                .env aanpassen database
                composer require laravel/jetstream
                php artisan jetstream:install livewire --teams

                npm install
                npm run build
                php artisan migrate -> Database eerst aanpassen

                php artisan serve

                php artisan make:model Post -m -c -f  -> (model and controller and factory (fake data))

                Post::factory()->count(10)->create();  -> in DatabaseSeeder
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
import AppLayout from '@/Layouts/AppLayout.vue';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

export default {
    props: {
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