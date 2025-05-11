<template>
    <Head title="Welcome" />
    <FrontendLayout :auth="auth" />
    <section
        class="min-h-screen bg-cover bg-center w-full h-full flex items-center hero relative"
        style="background-image: url('storage/files/1LNg0Sum6rv0fhoKVCFnhqScQuf2Bbrz7fZO1wh7.png')">
        <Container>
            <div class="relative z-10 text-lg">
                <h1 class="small pb-12">Your tickets</h1>

                <!-- Cart Items -->
                <div v-for="item in localCartitems" :key="item.id">
                    {{ item.name }} - {{ item.counter }} x {{ item.items.price }} = €
                    {{ (item.counter * parseFloat(item.items.price)).toFixed(2) }}
                </div>

                <!-- Total Price -->
                <p><strong>Totaal:</strong> €{{ totalPrice.toFixed(2) }}</p>

                <div class="grid grid-cols-12 gap-8" v-if="auth.user">
                    <div class="col-span-6 col-start-7">
                        <div class="bg-white/60 text-black rounded-[35px] p-8 h-full">
                            <span>{{ auth.user.plan_id }}</span><br>
                            <Link :href="route('purchase.index')">Products</Link><br>
                            <Link :href="route('items.index')">Items</Link><br>
                            <Link :href="route('profile.show')">Profile</Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-8 mt-8">
                    <div class="col-span-6">
                        <div v-if="!user" class="bg-white/60 rounded-[35px] p-8 h-full">
                            <h2 class="small mb-8">Login into your account</h2>
                            <input class="main--input w-full mb-4" type="text" v-model="form.email" placeholder="Login Email" />
                            <input class="main--input w-full mb-4" type="password" v-model="form.password" placeholder="Wachtwoord" />
                            <button class="btn btn--primary" @click.prevent="submitLogin">Login</button>
                        </div>
                        <div v-else class="bg-white/60 rounded-[35px] p-8 h-full">
                            <h2 class="small mb-8">You are logged in</h2>
                            <button class="btn btn--primary" @click.prevent="submitPayment">Bevestig Aankoop</button>
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div class="bg-white/60 rounded-[35px] p-8">
                            <h2 class="small mb-8">Continue as guest</h2>
                            <input class="main--input w-full mb-4" type="text" v-model="firstName" placeholder="Voornaam" />
                            <input class="main--input w-full mb-4" type="text" v-model="lastName" placeholder="Achternaam" />
                            <input class="main--input w-full mb-4" type="text" v-model="email" placeholder="Email" />
                            <button class="btn btn--primary" @click.prevent="submitPayment">Bevestig Aankoop</button>
                        </div>
                    </div>
                </div>
            </div>
        </Container>
    </section>

    <Container>
        <div class="hidden">
            <!-- Payment Info -->
            <input type="text" v-model="firstName" placeholder="Voornaam" />
            <input type="text" v-model="lastName" placeholder="Achternaam" />
            <input type="text" v-model="email" placeholder="Email" />
            <button @click.prevent="submitPayment">Bevestig Aankoop</button>

            <hr />

            <!-- Login Form -->
            <input type="text" v-model="form.email" placeholder="Login Email" />
            <input type="password" v-model="form.password" placeholder="Wachtwoord" />
            <label>
                <input type="checkbox" v-model="form.remember" />
                Onthoud mij
            </label>
            <button @click.prevent="submitLogin">Login</button>
        </div>
    </Container>
    <!--<Cart :cart="localCartitems" />-->
</template>

<script>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import Container from '../../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import Cart from '../../Components/Cart.vue';

export default {
    props: {
        guest_token: String,
        cartitems: {
            type: Array,
            default: () => [],
        },
        user: Object,
        auth: Object,
    },
    components: {
        Container,
        AppLayout,
        FrontendLayout,
        Cart,
    },
    setup(props) {
        const firstName = ref('');
        const lastName = ref('');
        const email = ref('');

        const localCartitems = ref([...props.cartitems]);

        // Optional: Watch for updates from parent/route change
        watch(
            () => props.cartitems,
            (newVal) => {
                // Uncomment next line to deduplicate if needed
                // localCartitems.value = Array.from(new Map(newVal.map(item => [item.id, item])).values());
                localCartitems.value = [...newVal];
            },
            { immediate: true }
        );

        const totalPrice = computed(() => {
            return localCartitems.value.reduce((sum, item) => {
                const price = parseFloat(item.items?.price ?? 0);
                return sum + (price * item.counter);
            }, 0);
        });

        const form = useForm({
            email: '',
            password: '',
            remember: false,
        });

        if (props.user) {
            firstName.value = props.user.name;
            lastName.value = 'develter';
            email.value = props.user.email;
        }

        const submitPayment = async () => {
            const payload = {
                first_name: firstName.value,
                last_name: lastName.value,
                email: email.value,
                items: localCartitems.value.map(item => ({
                    item_id: item.id,
                    name: item.name,
                    price: item.items?.price,
                    counter: item.counter,
                    line_total: parseFloat(item.items?.price ?? 0) * item.counter,
                })),
                total: totalPrice.value,
                guest_token: props.guest_token,
            };

            try {
                const response = await axios.post('/mollie/payment', payload);
                if (response.data?.checkoutUrl) {
                    window.open(response.data.checkoutUrl, '_blank');
                } else {
                    console.error('Geen checkout URL ontvangen van Mollie.');
                }
            } catch (error) {
                console.error('Fout bij Mollie betaling:', error);
            }
        };

        const submitLogin = () => {
            form.transform(data => ({
                ...data,
                remember: data.remember ? 'on' : '',
                cartitems: localCartitems.value,
            })).post(route('login'), {
                onFinish: () => {
                    form.reset('password');
                },
                onSuccess: () => {
                    window.location.reload();
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
            localCartitems,
        };
    },
};
</script>