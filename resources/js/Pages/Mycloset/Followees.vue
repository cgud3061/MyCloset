<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Inertia } from "@inertiajs/inertia";
import { usePage } from '@inertiajs/inertia-vue3';
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const props = defineProps ({
    user: Array,
    followees: Array,
});

function recomend () {
    Inertia.get('/dashboard');
};

function toMyPage (userId) {
    if (userId != usePage().props.value.auth.user.id) {
        Inertia.get('/mycloset/' + userId);
    } else {
        Inertia.get('/mycloset');
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ user.name }}がフォロー中</h2>
        </template>
        
        <div v-if="followees.length != 0">
            <div class="flex justify-center">
                <div class="container flex flex-col items-center justify-start mt-20 w-72 bg-white rounded-lg shadow">
                    <ul class="flex flex-col divide-y divide">
                        <li v-for="followee in followees" class="flex flex-row">
                            <div class="flex items-center flex-1 p-4 cursor-pointer select-none">
                                <div v-if="followee.profile_image_url != null" class="flex flex-col items-center justify-center w-10 h-10 mr-4">
                                    <button @click="toMyPage(followee.id)" type="button" class="relative block">
                                        <img alt="profil" :src="followee.profile_image_url" class="mx-auto object-cover rounded-full h-10 w-10 "/>
                                    </button>
                                </div>
                                <div v-else class="flex items-ceter justify-ceter w-14 h-14 mr-4">
                                    <div class="flex justify-center items-center aspect-square w-full m-2 rounded-full ring-2 ring-gray-200">
                                        <FontAwesomeIcon @click="toMyPage(followee.id)" icon="user" class="w-2/3 h-2/3"></FontAwesomeIcon>
                                    </div>
                                </div>
                                <div class="flex-1 pl-1 mr-16">
                                    <a @click="toMyPage(followee.id)" class="font-medium dark:text-white">
                                        {{ followee.name }}
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div v-else class="flex justify-center items-center w-full h-4/5">
            <div class="w-96 h-80 m-20">
                <FontAwesomeIcon class="w-40 h-40 justify-self-center mx-28" icon="user-plus"></FontAwesomeIcon>
                <h1 class="text-2xl text-black mx-2">フォロー中のユーザーはいません</h1>
                <button @click="recomend" class="text-base text-blue-600 w-40 mx-28 my-2">おすすめへ移動</button>
            </div>
        </div>
</AuthenticatedLayout>
</template>
