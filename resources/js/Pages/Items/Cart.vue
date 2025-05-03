<template>
    <Head title="Welcome" />
    <FrontendLayout></FrontendLayout>
    <section class="min-h-screen bg-cover bg-center w-full h-full flex items-center hero relative" style="background-image: url('storage/files/1LNg0Sum6rv0fhoKVCFnhqScQuf2Bbrz7fZO1wh7.png')">
        <Container>
            <div class="relative z-10 text-lg">
                <h1 class="small pb-12">Your info</h1>
                <!-- Cart Items -->
                <div v-for="item in cartitems" :key="item.id">
                    {{ item.name }} - {{ item.counter }} x {{ item.items.price }} = €{{ (item.counter * item.items.price).toFixed(2) }}
                </div>
                <!-- Total Price -->
                <p><strong>Totaal:</strong> €{{ totalPrice.toFixed(2) }}</p>
                <div class="grid grid-cols-12 gap-8 mt-20">
                    <div class="col-span-6">
                        <div v-if="!user" class="bg-white/60 rounded-[35px] p-8 h-full">
                            <h2 class="small mb-8">Login into your account</h2>
                            <input class="main--input w-full mb-4" type="text" v-model="form.email" placeholder="Login Email" />
                            <input class="main--input w-full mb-4" type="password" v-model="form.password" placeholder="Wachtwoord" />
                            <!-- Payment Button -->
                            <button class="btn btn--primary" @click.prevent="submitLogin">Login</button>
                        </div>
                        <div v-else class="bg-white/60 rounded-[35px] p-8 h-full">
                            <h2 class="small mb-8">Your are logged in</h2>
                            <button class="btn btn--primary" @click.prevent="submitPayment">Bevestig Aankoop</button>
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div class="bg-white/60 rounded-[35px] p-8">
                            <h2 class="small mb-8">Continue as guest</h2>
                            <input class="main--input w-full mb-4" type="text" v-model="firstName" placeholder="Voornaam" />
                            <input class="main--input w-full mb-4" type="text" v-model="lastName" placeholder="Achternaam" />
                            <input class="main--input w-full mb-4" type="text" v-model="email" placeholder="Email" />

                            <!-- Payment Button -->
                            <button class="btn btn--primary" @click.prevent="submitPayment">Bevestig Aankoop</button>
                        </div>
                    </div>
                </div>
            </div> 
        </Container>
    </section>
    <Container>
        <!-- Payment Info -->
        <input type="text" v-model="firstName" placeholder="Voornaam" />
        <input type="text" v-model="lastName" placeholder="Achternaam" />
        <input type="text" v-model="email" placeholder="Email" />

        <!-- Payment Button -->
        <button @click.prevent="submitPayment">
            Bevestig Aankoop
        </button>

        <hr />

        <!-- Login Form -->
        <input type="text" v-model="form.email" placeholder="Login Email" />
        <input type="password" v-model="form.password" placeholder="Wachtwoord" />
        <label>
            <input type="checkbox" v-model="form.remember" />
            Onthoud mij
        </label>

        <!-- Login Button -->
        <button @click.prevent="submitLogin">
            Login
        </button>
    </Container>
</template>

<script>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3'; // <- make sure you have @inertiajs/inertia-vue3 installed
import axios from 'axios';
import Container from '../../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

export default {
    props: {
        guest_token: String,
        cartitems: Array,
        user: Object,
    },
    components: {
        Container,
        AppLayout,
        FrontendLayout,
    },
    setup(props) {
        // Payment form data
        const firstName = ref('');
        const lastName = ref('');
        const email = ref('');

        const totalPrice = computed(() => {
            return props.cartitems.reduce((sum, item) => {
                return sum + (item.items.price * item.counter);
            }, 0);
        });

        // Inertia login form
        const form = useForm({
            email: '',
            password: '',
            remember: false,
        });

        /*if(props.user) {
            firstName.value = props.user.name;
            lastName.value = 'develter';
            email.value = props.user.email;
            console.log(props.user);
        }*/

        // Submit Payment
        const submitPayment = async () => {
            const payload = {
                first_name: firstName.value,
                last_name: lastName.value,
                email: email.value,
                items: props.cartitems.map(item => ({
                    item_id: item.id,
                    name: item.name,
                    price: item.items.price,
                    counter: item.counter,
                    line_total: item.items.price * item.counter,
                })),
                total: totalPrice.value,
                guest_token: props.guest_token,
            };

            try {
                const response = await axios.post('/mollie/payment', payload);
                console.log('Mollie response:', response);
                if (response.data?.checkoutUrl) {
                    window.open(response.data.checkoutUrl, '_blank');
                } else {
                    console.error('Geen checkout URL ontvangen van Mollie.');
                }
            } catch (error) {
                console.error('Fout bij Mollie betaling:', error);
            }
        };

        // Submit Login
        const submitLogin = () => {
            form.transform(data => ({
                ...data,
                remember: data.remember ? 'on' : '',
                cartitems: props.cartitems,
            })).post(route('login'), {
                onFinish: () => {
                    form.reset('password');
                },
            });
        };

        return {
            firstName,
            lastName,
            email,
            totalPrice,
            submitPayment,
            form,
            submitLogin,
        };
    },
};
</script>