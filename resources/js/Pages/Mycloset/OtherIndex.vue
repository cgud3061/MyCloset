<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from "@inertiajs/inertia";
import { ref, reactive, computed, onMounted } from 'vue';

// IteμControllerから受け取ったデータ
const props = defineProps ({
    user: Array,
    otherUser: Array,
    isFollowing: Boolean,
    followees: Number,
    followers: Number,
    items: Array,
    types: Array,
    categories: Array,
    brands: Array,
});

// 投稿者をフォローしていればTrue
let Following = ref(props.isFollowing);

let followers = ref(props.followers);

// itemsテーブルに一つでも洋服を追加していればtrue
const hasItems = props.items.length != 0;

// 洋服詳細の表示を制御する
const showDescription = ref(false);

let selectedItemId = ref(1);

function openDescription (index) {
    showDescription.value = true;
    selectedItemId.value = index;
};

function closeDescription () {
    showDescription.value = false;
};

function follow () {
    Following.value = !Following.value;
    
    axios.post('/follow/' + props.user.id)
    .then((response) => {
        // resoponseに対する処理を記述
        followers.value = response.data;
    });
};

// 投稿者のフォロワーを確認するメソッド
function checkFollowers () {
    Inertia.get('/followers/' + props.user.id);
}

// 投稿者をフォローしているユーザーを確認するメソッド
function checkFollowees () {
    Inertia.get('/followees/' + props.user.id);
}
</script>

<template>
    <Head title="MyCloset" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">MyCloset</h2>
        </template>
        
        <div class="bg-white">
            <div class="mx-auto max-w-2xl py-4 px-4 sm:py-4 sm:px-6 lg:max-w-7xl lg:px-8">
                <div class="relative flex bg-white rounded-md border border-gray-200 w-full h-60">
                    <div class="flex justify-center items-center w-1/5">
                        <img class="aspect-square w-10/12 m-2 rounded-full ring-2 ring-white" :src="user.profile_image_url" alt="">
                    </div>
                    <div class="relative w-4/5">
                        <div class="flex h-11/5">
                            <h1 class="text-4xl m-2">{{ user.name }}</h1>
                            <div class="w-1/5 m-2">
                                <button v-if="Following" @click="follow" type="submit" class="justify-center items-center h-10/12 w-10/12 rounded-md border border-transparent bg-indigo-600 py-2 px-4 m-1 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">フォロー中</button>
                                <button v-else @click="follow" type="submit" class="justify-center items-center h-10/12 w-10/12 rounded-md border border-transparent bg-indigo-600 py-2 px-4 m-1 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">フォローする</button>
                            </div>
                        </div>
                        <div class="flex justify-start items-start h-1/5">
                            <button @click="checkFollowers" type="submit" class="m-1 text-sm font-medium text-glay-300">フォロワー<span class="mx-1">{{ followers }}</span></button>
                            <button @click="checkFollowees" type="submit" class="m-1 text-sm font-medium text-glay-300">フォロー中<span class="mx-1">{{ followees }}</span></button>
                        </div>
                        <h1 class="text-base m-2">{{ user.profile }}</h1>
                    </div>
                </div>
                
                <!-- 洋服一覧部分 -->
                <!-- itemsテーブルに洋服を登録しているかどうかで条件分岐 -->
                <div v-if="hasItems">
                    <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                        <!-- 洋服要素 -->
                        <div v-for="(item, index) in items" :key="item.name" class="group relative">
                            <!-- 写真要素 -->
                            <div class="relative min-h-80 aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none lg:h-80">
                                <img :src="item.image_url" class="absolute z-0 h-full w-full object-cover object-center lg:h-full lg:w-full z-0">
                                <button @click="openDescription(index)" class="absolute z-10 h-full w-full object-cover object-center hover:opacity-75"></button>
                            </div>
                            <!-- 商品名など -->
                            <div class="mt-4 flex justify-between">
                                <div>
                                    <h3 class="text-xs text-gray-500">
                                        <a href="#">
                                            <span aria-hidden="true" class="absolute inset-0"></span>
                                            {{ item.name }}
                                        </a>
                                    </h3>
                                    <span class="inline mt-1 mr-2 text-sm text-gray-300 text-xs">{{ types[item.type_id-1].name }}</span>
                                    <span class="inline mt-1 mr-2 text-sm text-gray-300 text-xs">{{ categories[item.categorie_id-1].name }}</span>
                                    <span class="inline mt-1 mr-2 text-sm text-gray-300 text-xs">{{ brands[item.brand_id-1].name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 洋服詳細モーダル -->
        <div v-if="hasItems">
            <div v-show="showDescription" class="relative z-10" role="dialog" aria-modal="true">
                <!-- オーバーレイ要素 -->
                <div class="fixed inset-0 hidden bg-gray-500 bg-opacity-75 transition-opacity md:block"></div>
                <!-- モーダルの土台 -->
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <!-- モーダルの幅などの設定 -->
                    <div class="flex min-h-full items-stretch justify-center text-center md:items-center md:px-2 lg:px-4">
                        <!-- モーダル要素の設定 -->
                        <div class="flex w-full transform text-left text-base transition md:my-8 md:max-w-2xl md:px-4 lg:max-w-4xl">
                            <!-- モーダル要素 -->
                            <div class="relative flex w-full items-center overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8">
                                <!-- 閉じるボタン -->
                                <button @click="closeDescription" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
                                    <span class="sr-only">Close</span>
                                    <!-- xマーク -->
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <!-- モーダルの内容 -->
                                <div class="grid w-full grid-cols-1 items-start gap-y-8 gap-x-6 sm:grid-cols-12 lg:gap-x-8">
                                    <div class="aspect-w-2 aspect-h-3 overflow-hidden rounded-lg bg-gray-100 sm:col-span-8 lg:col-span-7">
                                        <img :src="items[selectedItemId].image_url" alt="Two each of gray, white, and black shirts arranged on table." class="h-full w-full object-cover object-center">
                                    </div>
                                    <div class="h-full mb-4 sm:col-span-4 lg:col-span-5">
                                        <h2 class="text-lg font-bold text-gray-500 sm:pr-12">{{ items[selectedItemId].name }}</h2>
                                        <p class="mr-2 text-base text-gray-500">種別：{{ types[items[selectedItemId].type_id-1].name }}</p>
                                        <p class="mr-2 text-base text-gray-500">ブランド名：{{ brands[items[selectedItemId].brand_id-1].name }}</p>
                                        <p class="mr-2 text-base text-gray-500">カテゴリー：{{ categories[items[selectedItemId].categorie_id-1].name }}</p>
                                        <p class="text-base text-gray-500">価格：{{ items[selectedItemId].price }}円</p>
                                        <h3 class="mt-3 mb-1 text-base text-gray-500">説明：</h3>
                                        <text class="mb-2 text-sm text-gray-500">{{ items[selectedItemId].description }}</text>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>