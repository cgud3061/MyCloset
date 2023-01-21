<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 pt-0 bg-hero-img bg-cover">
        <div>
            <Link href="/">
                <img src="https://res.cloudinary.com/dxe6weecx/image/upload/v1673686725/logo_e29jid.png" class="block h-20 w-20">
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
