<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps ({
    user: Array,
    followees: Array,
});

console.log(props.followees);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">フォローしているユーザー</h2>
        </template>
        
        <div v-for="followee in followees">{{ followee.name }}</div>
        
</AuthenticatedLayout>
</template>
