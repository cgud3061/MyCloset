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

let selectedItem = ref(props.items[0]);

function openDescription (item) {
    showDescription.value = true;
    selectedItem.value = item;
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

function showItems () {
    Inertia.get('/mycloset/' + props.user.id);
}

function showOutfits () {
    Inertia.get('/myposts/' + props.user.id);
}
</script>

<template>
    <Head title="MyCloset" />

    <AuthenticatedLayout>

        <div class="bg-white h-screen">
            <div class="mx-auto max-w-2xl py-10 px-4 sm:py-10 sm:px-6 lg:max-w-7xl lg:px-8">
                <div class="relative flex bg-white rounded-md border border-gray-200 w-full h-60">
                    <div v-if="user.profile_image_url != null" class="flex justify-center items-center w-1/5">
                        <img class="aspect-square w-10/12 m-2 rounded-full ring-2 ring-gray-200" :src="user.profile_image_url" alt="">
                    </div>
                    <div v-else class="flex justify-center items-center w-1/5">
                        <div class="flex justify-center items-center aspect-square w-10/12 m-2 rounded-full ring-2 ring-gray-200">
                            <FontAwesomeIcon icon="user" class="w-2/3 h-2/3"></FontAwesomeIcon>
                        </div>
                    </div>
                    <div class="relative w-4/5">
                        <div class="flex h-11/5">
                            <h1 class="text-4xl m-2">{{ user.name }}</h1>
                            <div class="w-1/5 m-2">
                                <PrimaryButton v-if="Following" @click="follow" type="submit" class="">フォロー中</PrimaryButton>
                                <PrimaryButton v-else @click="follow" type="submit" class="">フォローする</PrimaryButton>
                            </div>
                        </div>
                        <div class="flex justify-start items-start h-1/5">
                            <button @click="checkFollowers" type="submit" class="m-1 text-sm font-medium text-glay-300">フォロワー<span class="mx-1">{{ followers }}</span></button>
                            <button @click="checkFollowees" type="submit" class="m-1 text-sm font-medium text-glay-300">フォロー中<span class="mx-1">{{ followees }}</span></button>
                        </div>
                        <h1 class="text-base m-2">{{ user.profile }}</h1>
                    </div>
                </div>
                
                <div class="flex relative justify-start items-center  mt-5 h-11 w-full border border-t-0 border-r-0 border-l-0 border-b-2 border-gray-200">
                    <div class="flex justify-center items-center">
                        <button @click="showItems" class="p-2 text-base font-medium text-gray-500 border boeder-t-2 border-r-2 border-l-2 border-b-0">アイテム</button>
                        <button @click="showOutfits" class="p-2  text-base font-medium text-gray-500">コーデ</button>
                    </div>
                </div>

                <!-- 洋服一覧部分 -->
                <!-- itemsテーブルに洋服を登録しているかどうかで条件分岐 -->
                <div v-if="hasItems">
                    <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                        <!-- 洋服要素 -->
                        <div @click="openDescription(item)" v-for="(item, index) in items" :key="item.name" class="group relative">
                            <!-- 写真要素 -->
                            <div class="min-h-80 aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none lg:h-80">
                                <img :src="item.image_url" class="h-full w-full object-cover object-center lg:h-full lg:w-full">
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
                                    <span class="inline mt-1 mr-2 text-sm text-gray-300 text-xs">{{ item.type.name }}</span>
                                    <span class="inline mt-1 mr-2 text-sm text-gray-300 text-xs">{{ item.categorie.name }}</span>
                                    <span class="inline mt-1 mr-2 text-sm text-gray-300 text-xs">{{ item.brand.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="flex justify-center items-center w-full h-4/5">
                        <div class="w-80 h-80 m-20">
                            <FontAwesomeIcon class="w-40 h-40 justify-self-center mx-20" icon="shirt"></FontAwesomeIcon>
                            <h1 class="text-2xl text-black mx-10 w-60 ">アイテムがありません</h1>
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
                                        <img :src="selectedItem.image_url" alt="Two each of gray, white, and black shirts arranged on table." class="h-full w-full object-cover object-center">
                                    </div>
                                    <div class="h-full mb-4 sm:col-span-4 lg:col-span-5">
                                        <h2 class="text-lg font-bold text-gray-500 sm:pr-12">{{ selectedItem.name }}</h2>
                                        <p class="mr-2 text-base text-gray-500">種別：{{ selectedItem.type.name }}</p>
                                        <p class="mr-2 text-base text-gray-500">ブランド名：{{ selectedItem.brand.name }}</p>
                                        <p class="mr-2 text-base text-gray-500">カテゴリー：{{ selectedItem.categorie.name }}</p>
                                        <p class="text-base text-gray-500">価格：{{ selectedItem.price }}円</p>
                                        <h3 class="mt-3 mb-1 text-base text-gray-500">説明：</h3>
                                        <text class="mb-2 text-sm text-gray-500">{{ selectedItem.description }}</text>
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