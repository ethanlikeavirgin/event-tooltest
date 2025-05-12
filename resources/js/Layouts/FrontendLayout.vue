<template>
    <nav>
        <Container>
            <div class="bg-black/50 p-5 rounded-[35px] fixed left-0 right-0 top-4 max-w-5xl mx-auto w-full z-50 flex flex-row items-center main-navbar">
                <Logo></Logo>
                <ul class="text-base font-medium md:flex hidden gap-10 text-white items-end justify-end ml-auto pr-32">
                    <li>Events</li>
                    <li>Start selling</li>
                    <li>About</li>
                    <li>Contact</li>
                </ul>
                <div class="mobile-opener flex flex-col justify-end ml-auto mr-4" :class="{ 'active': mobileMenuOpen }" @click="toggleMobileMenu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <transition name="mobile-menu">
                    <div class="absolute top-full right-0 bg-black/50 text-white p-8 px-12 rounded-[30px] mobile-opener__content" v-show="mobileMenuOpen">
                        <ul>
                            <li>Events</li>
                            <li>Start selling</li>
                            <li>About</li>
                            <li>Contact</li>
                        </ul>
                    </div>
                </transition>
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
    data() {
        return {
            mobileMenuOpen: false,
        };
    },
    methods: {
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },
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