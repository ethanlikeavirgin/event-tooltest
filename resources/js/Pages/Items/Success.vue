<template>
    <Container>
        <div>
            <h1>Bedankt voor je aankoop!</h1>
            <div>Order nummer: {{ current_id }}</div>
            <div v-if="orders">
                <div>{{ orders.id }}</div>
                <div v-for="order in orders.items">
                    {{ order.item_id }} - {{ order.name }} - {{ order.price }} - {{ order.counter }} - {{ order.line_total }}
                </div>
                <div>{{ orders.total }}</div>
            </div>
        </div>
    </Container>
</template>
<script>
import Container from '../../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        current_id: Object,
        orders: Object
    },
    components: {
        Container,
        AppLayout,
    },
}
</script>