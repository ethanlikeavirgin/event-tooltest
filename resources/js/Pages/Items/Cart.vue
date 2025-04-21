<template>
    <Container>
        <div><span>Dit is een test - </span>{{ guest_token }}</div>

        <div v-for="item in cartitems" :key="item.id">
            {{ item.name }} - {{ item.counter }} x {{ item.items.price }} = €
            {{ (item.counter * item.items.price).toFixed(2) }}
        </div>

        @csrf
        <button @click.prevent="submit">
            Bevestig Aankoop
        </button>
    </Container>
</template>

<script>
import { ref, computed } from 'vue';
import axios from 'axios'; // ← make sure axios is imported
import { useForm } from '@inertiajs/vue3';
import Container from '../../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        guest_token: Object,
        cartitems: Array,
    },
    components: {
        Container,
        AppLayout,
    },
    setup(props) {
        const totalPrice = computed(() => {
            return props.cartitems.reduce((sum, item) => {
                return sum + (item.items.price * item.counter);
            }, 0);
        });

        const submit = async () => {
            if (!props.cartitems.length) {
                console.error("Geen items in het winkelmandje.");
                return;
            }

            const payload = {
                items: props.cartitems.map((item) => ({
                    item_id: item.id,
                    name: item.name,
                    price: item.items.price,
                    counter: item.counter,
                    line_total: (item.items.price * item.counter).toFixed(2),
                })),
                total: totalPrice.value.toFixed(2),
                guest_token: props.guest_token,
            };

            try {
                const response = await axios.post('/mollie/payment', payload);

                if (response.data.checkoutUrl) {
                    window.location.href = response.data.checkoutUrl;
                } else {
                    console.error("Geen checkout URL ontvangen van Mollie.");
                }
            } catch (err) {
                console.error("Fout bij starten van betaling:", err);
            }
        };

        return {
            submit,
            totalPrice,
        };
    },
};
</script>
