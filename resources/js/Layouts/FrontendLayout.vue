<template>
    <nav>
        <Container>
            <div class="bg-black/50 p-5 rounded-[35px] fixed left-0 right-0 top-4 max-w-5xl mx-auto w-full z-50 flex flex-row items-center">
                <Logo></Logo>
                <ul class="text-base font-medium flex gap-10 text-white items-end justify-end ml-auto pr-32">
                    <li>Events</li>
                    <li>Start selling</li>
                    <li>About</li>
                    <li>Contact</li>
                </ul>
            </div>
        </Container>
    </nav>
    <div v-if="auth.user">
        <div class="fixed top-4 right-10 z-50">
            <form @submit.prevent="logout">
                <button type="submit" class="btn btn--primary small">Logout</button>
            </form>
        </div>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';
import Container from '../Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Logo from '../Components/Logo.vue';

export default {
    props: {
        guest_token: String,
        auth: Object,
    },
    components: {
        Container,
        Logo,
        AppLayout,
    },
    methods: {
        logout() {
            router.post(route('logout'), {}, {
                onSuccess: () => {
                    window.location.href = '/';
                }
            });
        }
    }
}
</script>