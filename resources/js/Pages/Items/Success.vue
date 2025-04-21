<template>
    <Container>
        <div>
            <h1>Bedankt voor je aankoop!</h1>
            <div>Order nummer: {{ current_id }}</div>
        </div>
    </Container>
</template>
<script>
import Container from '../../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        current_id: Object,
    },
    components: {
        Container,
        AppLayout,
    },
}
</script>