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
        class="relative flex items-center justify-center min-h-screen bg-hero-img bg-cover sm:items-center sm:pt-0"
    >
        <div v-if="canLogin" class="fixed top-0 right-0 px-6 py-4 block">
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
            <div class="flex justify-center w-full">
                <img src="https://res.cloudinary.com/dxe6weecx/image/upload/v1673686725/logo_e29jid.png" class="block h-60 w-60">
            </div>
            <div class="w-full mt-10">
                <div class="flex justify-center w-full">
                    <h1 class="text-xl text-white bg-black bg-opacity-70 rounded p-2 font-medium mx-3 mt-2">いつの間にか着ない服が増えていた...</h1>
                </div>
                <div class="flex justify-center w-full">
                    <h1 class="text-xl text-white bg-black bg-opacity-70 rounded p-2 font-medium mx-3 my-2">自分のコーディネートに自信がない...</h1>
                </div>
                <div class="flex justify-center w-full">
                    <h1 class="text-xl text-white bg-black bg-opacity-70 rounded p-2 font-medium mx-3 mb-2">マイクロは洋服管理機能とコーディネート投稿機能でそんなあなたのお悩みを解決します!</h1>
                </div>
            </div>
        </div>
        
    </div>
</template>
