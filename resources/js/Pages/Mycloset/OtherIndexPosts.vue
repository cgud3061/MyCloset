<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from "@inertiajs/inertia";
import { ref, reactive, computed, onMounted } from 'vue';

import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Virtual } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

// IteμControllerから受け取ったデータ
const props = defineProps ({
    user: Array,
    otherUser: Array,
    isFollowing: Boolean,
    followees: Number,
    followers: Number,
    posts: Array,
});

// itemsテーブルに洋服を一つでも追加してればtrue
const hasPosts = props.posts.length != 0;

// コーディネートの詳細表示
const urls = ref(null);

const selectedPost = ref(props.posts[0]);

const showDescription = ref(false);

function openDescription (post) {
    selectedPost.value = post;
    urls.value = selectedPost.value.images;
    showDescription.value = true;
};

function closeDescription () {
    showDescription.value = false;
};

let Following = ref(props.isFollowing);

let followers = ref(props.followers);

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
                            <button @click="checkFollowers" type="submit" class="m-1 text-sm font-medium text-gray-500">フォロワー<span class="mx-1">{{ followers }}</span></button>
                            <button @click="checkFollowees" type="submit" class="m-1 text-sm font-medium text-gray-500">フォロー中<span class="mx-1">{{ followees }}</span></button>
                        </div>
                        <div class="absolute flex  bottom-0 right-0 justify-end mx-auto my-2 w-full h-1/5">
                        </div>
                        <h1 class="text-base m-2">{{ user.profile }}</h1>
                    </div>
                </div>
                
                <div class="flex relative justify-start items-center  mt-5 h-11 w-full border border-t-0 border-r-0 border-l-0 border-b-2 border-gray-200">
                    <div class="flex justify-center items-center">
                        <button @click="showItems" class="p-2 text-base font-medium text-gray-500">アイテム</button>
                        <button @click="showOutfits" class="p-2  text-base font-medium text-gray-500 border boeder-t-2 border-r-2 border-l-2 border-b-0">コーデ</button>
                    </div>
                </div>
                
                <!-- 投稿一覧部分 -->
                <!-- postsテーブルに洋服を登録しているかどうかで条件分岐 -->
                <div v-if="hasPosts">
                    <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                        <!-- 洋服要素 -->
                        <div @click="openDescription(post)" v-for="(post, index) in posts" :key="posts.title" class="group relative">
                            <!-- 写真要素 -->
                            <div class="min-h-80 aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none lg:h-80">
                                <img :src="post.images[0].image_url" class="h-full w-full object-cover object-center lg:h-full lg:w-full">
                            </div>
                            <!-- 商品名など -->
                            <div class="mt-4 flex justify-between">
                                <div>
                                    <h3 class="text-xs text-gray-500">
                                        <a href="#">
                                            <span aria-hidden="true" class="absolute inset-0"></span>
                                            {{ post.title }}
                                        </a>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="flex justify-center items-center w-full h-4/5">
                        <div class="w-96 h-80 m-20">
                            <FontAwesomeIcon class="w-40 h-40 justify-self-center mx-28" icon="shirt"></FontAwesomeIcon>
                            <h1 class="text-2xl text-black w-80 mx-8">コーディネートがありません</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="hasPosts">
            <!-- 投稿詳細モーダル -->
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
                                    <!-- サムネイル部分 -->
                                    <div class="aspect-w-2 aspect-h-3 overflow-hidden rounded-lg bg-gray-100 sm:col-span-8 lg:col-span-7">
                                        <swiper
                                            :modules="[Navigation, Pagination, Virtual]"
                                            navigation
                                            :pagination="{ clickable: true }"
                                            virtual
                                        >
                                            <swiperSlide v-for="(url, index) in urls" :virtualIndex="index" :key="`slide-${index}`">
                                                <img :src="url.image_url" class="h-full w-full object-cover object-center"/>
                                            </swiperSlide>
                                        </swiper>
                                    </div>
                                    <!-- フォーム部分 -->
                                    <div class="h-full mb-4 sm:col-span-4 lg:col-span-5">
                                        <h2 class="text-xl font-bold text-gray-600 sm:pr-12">{{ selectedPost.title }}</h2>
                                        <h2 class="tex-lg font-medium text-gray-500 mt-2">説明</h2>
                                        <h2 class="text-base text-gray-500 sm:pr-12">{{ selectedPost.body }}</h2>
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