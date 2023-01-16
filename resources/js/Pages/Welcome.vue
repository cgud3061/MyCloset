<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div
        class="relative flex items-top justify-center min-h-screen bg-hero-img bg-cover sm:items-center sm:pt-0"
    >
        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="text-2xl text-white underline"
                >ようこそ</Link
            >

            <template v-else>
                <Link :href="route('login')" class="text-2xl text-white  underline">ログイン</Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 text-2xl text-white underline"
                    >登録</Link
                >
            </template>
        </div>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <img src="https://res.cloudinary.com/dxe6weecx/image/upload/v1673686725/logo_e29jid.png" class="block h-60 w-60">
        </div>
    </div>
</template>
