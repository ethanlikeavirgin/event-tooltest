<template>
    <section class="py-32">
        <Container>
            <h2 class="text-center font-bold pb-24">Checkout our <span class="text-primary">prices</span></h2>
            <div class="grid grid-cols-12 gap-6">
                <div v-for="plan in plans" class="lg:col-span-4 md:col-span-6 col-span-12">
                    <div class="main-card primary">
                        <span>{{ plan.name }}</span>
                        <div class="flex flex-row gap-2 items-end pt-4">
                            <h2>€ {{ plan.price }}</h2>
                            <span>/once</span>
                        </div>
                        <a class="btn btn--white w-full mt-8 text-center !rounded-xl" @click="purchasePlan(plan)">Order now</a>
                        <div class="mt-8">
                            <div class="plan-options" v-html="plan.options"></div>
                        </div>
                    </div>
                </div>
            </div>
        </Container>
    </section>
</template>

<script>
import { router } from '@inertiajs/vue3'
import axios from 'axios';
import Container from '../Components/Container.vue';

export default {
    props: {
        plans: Array,
    },
    components: {
        Container,
    },
    methods: {
        async purchasePlan(plan) {
            const payload = {
                plan_id: plan.id
            };

            try {
                const response = await axios.post('/mollie/payment', payload, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Inertia': false, // Important!
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (response.data?.checkoutUrl) {
                    window.location.href = response.data.checkoutUrl;
                } else {
                    console.error('No checkout URL returned from Mollie.');
                }
            } catch (error) {
                console.error('Error during Mollie payment:', error);
            }
        }
    }
}
</script>