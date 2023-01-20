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
    followees: Number,
    followers: Number,
    items: Array,
    posts: Array,
    errors: Object,
});

// itemsテーブルに洋服を一つでも追加してればtrue
const hasPosts = props.posts.length != 0;

// コーディネートの詳細表示
const urls = ref(null);

const selectedPost = ref([]);

const showDescription = ref(false);

console.log(props.posts);

function openDescription (post) {
    selectedPost.value = post;
    urls.value = selectedPost.value.images;
    showDescription.value = true;
};

function closeDescription () {
    showDescription.value = false;
};

// 投稿作成関連
const form = reactive({
    title: null,
    body: null,
    images: null,
});

const previewSrc = ref(['https://res.cloudinary.com/dxe6weecx/image/upload/v1671761587/%E5%86%99%E7%9C%9F%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B34_cughdj.png']);

const showPostForm = ref(false);

function openPostForm () {
  showPostForm.value = true;
};

function closePostForm () {
  showPostForm.value = false;
};

function submit () {
    closePostForm();
    console.log(form);
    Inertia.post("/post/" + props.user.id, form);
};

// 投稿編集機能
const showUpdateForm = ref(false);

function openUpdateForm () {
    console.log(selectedPost.value);
    closeDescription();
    form.title = selectedPost.value.title;
    form.body = selectedPost.value.body;
    showUpdateForm.value = true;
};

function closeUpdateForm () {
    showUpdateForm.value = false;
};

function update () {
    closeUpdateForm();
    console.log("aaa");
    Inertia.put("/post/" + selectedPost.value.id, form);
};

// 洋服削除
function deleteItem (deleteItemId) {
    closeDescription();
    Inertia.delete("/post/" + selectedPost.value.id);
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
    Inertia.get('/mycloset');
}

function showOutfits () {
    Inertia.get('/myposts');
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
                        </div>
                        <div class="flex justify-start items-start h-1/5">
                            <button @click="checkFollowers" type="submit" class="m-1 text-sm font-medium text-gray-500">フォロワー<span class="mx-1">{{ followers }}</span></button>
                            <button @click="checkFollowees" type="submit" class="m-1 text-sm font-medium text-gray-500">フォロー中<span class="mx-1">{{ followees }}</span></button>
                        </div>
                        <h1 class="text-base m-2">{{ user.profile }}</h1>
                    </div>
                </div>
                
                <div class="flex relative justify-start items-center  mt-5 h-11 w-full border border-t-0 border-r-0 border-l-0 border-b-2 border-gray-200">
                    <div class="flex justify-center items-center">
                        <button @click="showItems" class="p-2 text-base font-medium text-gray-500">アイテム</button>
                        <button @click="showOutfits" class="p-2  text-base font-medium text-gray-500 border boeder-t-2 border-r-2 border-l-2 border-b-0">コーデ</button>
                    </div>
                    <PrimaryButton @click="openPostForm" class="absolute right-0">コーデを追加</PrimaryButton>
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
                                        <h1 class="text-xs text-gray-400">いいね:{{ post.favorites_count }}</h1>
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
                            <button @click="openPostForm" class="w-40 text-base text-blue-600 mx-28 my-2">コーディネートを作成</button>
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
                                    <div class="absolute bottom-0 right-0 m-4">
                                        <button @click="openUpdateForm" type="submit" class="justify-center m-1 rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">編集</button>
                                        <button @click="deleteItem" type="submit" class="justify-center m-1 rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">削除</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <!-- 投稿作成モーダル -->
        <div v-show="showPostForm" class="relative z-10" role="dialog" aria-modal="true">
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
                            <button @click="closePostForm" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
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
                                    <div v-if="form.images">
                                        <swiper
                                            :modules="[Navigation, Pagination, Virtual]"
                                            navigation
                                            :pagination="{ clickable: true }"
                                            virtual
                                        >
                                            <swiperSlide v-for="(url, index) in form.images" :virtualIndex="index" :key="`slide-${index}`">
                                                <img :src="url" class="w-full h-full object-cover object-center"/>
                                            </swiperSlide>
                                        </swiper>
                                    </div>
                                    <div v-else>
                                        <div class="flex justify-center items-center w-96 h-96">
                                            <FontAwesomeIcon class="w-40 h-40" icon="image"></FontAwesomeIcon>
                                        </div>
                                    </div>
                                </div>
                                <!-- フォーム部分 -->
                                <div class="h-full mb-4 mr-5 sm:col-span-4 lg:col-span-5">
                                    <form @submit.prevent="submit" enctype="multipart/form-data">
                                        <div class="overflow-hidden sm:rounded-md">
                                            <div class="bg-white px-4 py-5 sm:p-6">
                                                <div class="">
                                                    <label for="post_title" class="block my-1 text-sm font-medium text-gray-700">タイトル</label>
                                                    <div v-if="errors.title" class="my-1 text-xs font-medium text-red-500">タイトルを入力してください</div>
                                                    <input v-model="form.title" type="text" name="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                                <div class="">
                                                    <label for="post_body" class="block my-1 text-sm mt-2 font-medium text-gray-700">説明</label>
                                                    <div v-if="errors.body" class="my-1 text-xs font-medium text-red-500">説明を入力してください</div>
                                                    <input v-model="form.body" type="text" name="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                                <div class="">
                                                    <label for="post_title" class="block my-1 text-sm mt-2 font-medium text-gray-700">アイテム</label>
                                                    <div v-if="errors.images" class="my-1 text-xs font-medium text-red-500">アイテムを選択してください</div>
                                                    <label class="my-1 text-xs ml-2 font-medium text-gray-600">Shiftキー or Comandキーで複数選択</label>
                                                    <select v-model="form.images"  multiple name="type" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                        <option v-for="item in items" :key="item.name" :value="item.image_url">{{ item.name }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="absolute bottom-0 right-0 m-4">
                                        <button @click="submit" type="submit" class="justify-center m-1 rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">投稿</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 投稿編集モーダル -->
        <div v-show="showUpdateForm" class="relative z-10" role="dialog" aria-modal="true">
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
                            <button @click="closeUpdateForm" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
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
                                    <div v-if="form.images">
                                        <swiper
                                            :modules="[Navigation, Pagination, Virtual]"
                                            navigation
                                            :pagination="{ clickable: true }"
                                            virtual
                                        >
                                            <swiperSlide v-for="(url, index) in form.images" :virtualIndex="index" :key="`slide-${index}`">
                                                <img :src="url" class="h-full w-full object-cover object-center"/>
                                            </swiperSlide>
                                        </swiper>
                                    </div>
                                    <div v-else>
                                        <div class="flex justify-center items-center w-96 h-96">
                                            <FontAwesomeIcon class="w-40 h-40" icon="image"></FontAwesomeIcon>
                                        </div>
                                    </div>
                                </div>
                                <!-- フォーム部分 -->
                                <div class="h-full mb-4 mr-5 sm:col-span-4 lg:col-span-5">
                                    <form @submit.prevent="update" enctype="multipart/form-data">
                                        <div class="overflow-hidden sm:rounded-md">
                                            <div class="bg-white px-4 py-5 sm:p-6">
                                                <div class="">
                                                    <label for="post_title" class="block text-sm font-medium text-gray-700">タイトル</label>
                                                    <div v-if="errors.title" class="my-1 text-xs font-medium text-red-500">タイトルを入力してください</div>
                                                    <input v-model="form.title" type="text" name="street-address" :value="form.title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                                <div class="">
                                                    <label for="post_body" class="block text-sm mt-2 font-medium text-gray-700">説明</label>
                                                    <div v-if="errors.body" class="my-1 text-xs font-medium text-red-500">説明を入力してください</div>
                                                    <input v-model="form.body" type="text" name="street-address" :value="form.body" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                                <div class="">
                                                    <label for="post_title" class="block text-sm mt-2 font-medium text-gray-700">アイテム</label>
                                                    <div v-if="errors.images" class="my-1 text-xs font-medium text-red-500">アイテムを選択してください</div>
                                                    <label class="text-xs ml-2 font-medium text-gray-600">Shiftキー or Comandキーで複数選択</label>
                                                    <select v-model="form.images"  multiple name="type" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                        <option v-for="item in items" :key="item.name" :value="item.image_url">{{ item.name }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="absolute bottom-0 right-0 m-4">
                                            <button @click="closeUpdateForm" type="submit" class="justify-center m-1 rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">投稿</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>