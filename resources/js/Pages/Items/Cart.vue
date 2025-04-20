<template>
    <Container>
        <div><span>Dit is een test - </span>{{ guest_token }}</div>

        <div v-for="item in cartitems" :key="item.id">
            {{ item.name }} - {{ item.counter }} x {{ item.items.price }} = €
            {{ (item.counter * item.items.price).toFixed(2) }}
        </div>

        <button @click.prevent="submit">
            Bevestig Aankoop
        </button>
    </Container>
</template>

<script>
import { ref, computed } from 'vue';
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
        const form = useForm({
            items: [], // will contain all cart item data
            total_price: '',
        });

        const totalPrice = computed(() => {
            return props.cartitems.reduce((sum, item) => {
                return sum + (item.items.price * item.counter);
            }, 0);
        });

        const submit = () => {
            if (!props.cartitems.length) {
                console.error("Geen items in het winkelmandje.");
                return;
            }

            // Prepare the list of items to send
            form.items = props.cartitems.map((item) => ({
                item_id: item.id,
                name: item.name,
                price: item.items.price,
                counter: item.counter,
                line_total: (item.items.price * item.counter).toFixed(2),
            }));

            form.total_price = totalPrice.value.toFixed(2);

            form.post(route('cart.store'), {
                forceFormData: true,
                onFinish: () => {
                    console.log("Bestelling succesvol geplaatst.");
                },
                onError: (errors) => {
                    console.error("Fout bij verzenden:", errors);
                },
            });
        };

        return {
            form,
            submit,
            totalPrice,
        };
    },
};
</script>
