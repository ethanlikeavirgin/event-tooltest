<template>
    <Container>
        <div>{{ guest_token }}</div>

        <div v-for="item in cartitems" :key="item.id">
            {{ item.name }} - {{ item.counter }} x {{ item.items.price }} = €
            {{ (item.counter * item.items.price).toFixed(2) }}
        </div>

        <p><strong>Totaal:</strong> €{{ totalPrice.toFixed(2) }}</p>
        <input type="text" v-model="firstName" placeholder="Voornaam" />
        <input type="text" v-model="lastName" placeholder="Achternaam" />
        <button @click.prevent="submit">
            Bevestig Aankoop
        </button>
    </Container>
</template>

<script>
import { computed, ref } from 'vue';
import axios from 'axios';
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
        const firstName = ref('');
        const lastName = ref('');
        const totalPrice = computed(() => {
            return props.cartitems.reduce((sum, item) => {
                return sum + (item.items.price * item.counter);
            }, 0);
        });

        const submit = async () => {
            const payload = {
                first_name: firstName.value,
                last_name: lastName.value,
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
                console.log('Mollie response:', response); // ← debug this

                if (response.data?.checkoutUrl) {
                    window.open(response.data.checkoutUrl, '_blank');
                } else {
                    console.error('Geen checkout URL ontvangen van Mollie.');
                }
            } catch (error) {
                console.error('Fout bij Mollie betaling:', error);
            }
        };

        return {
            submit,
            totalPrice,
            firstName,
            lastName,
        };
    },
};
</script>
