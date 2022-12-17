<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps ({
    user: Array,
    followers: Array,
});

console.log(props.followers);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">フォローされているユーザー</h2>
        </template>
        
        <div v-for="follower in followers">{{ follower.name }}</div>
        
</AuthenticatedLayout>
</template>
