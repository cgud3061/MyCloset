<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, reactive, computed, onMounted } from 'vue';
import { Inertia } from "@inertiajs/inertia";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Virtual } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const props = defineProps ({
    user: Array,
    pleaseFollow: Boolean,
    posts: Array,
    items: Array,
});

console.log(props.pleaseFollow);

const urls = ref(null);

const selectedPost = ref([]);

const showDescription = ref(false);

function openDescription (post) {
    selectedPost.value = post;
    urls.value = selectedPost.value.images;
    console.log(selectedPost.value);
    showDescription.value = true;
};

function closeDescription () {
    showDescription.value = false;
};

const showPostForm = ref(false);

function openPostForm () {
  showPostForm.value = true;
};

function closePostForm () {
  showPostForm.value = false;
};

function submit () {
  console.log(form);
  Inertia.post("/post/" + props.user.id, form);
  closePostForm();
};

const form = reactive({
    title: null,
    body: null,
    images: null,
});

const previewSrc = ref(['https://res.cloudinary.com/dxe6weecx/image/upload/v1671761587/%E5%86%99%E7%9C%9F%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B34_cughdj.png']);

function uploadFile(event) {
    form.images = event.target.files;
    for (let i = 0; i < form.images.length; i++) {
        var fileReader=new FileReader();
        fileReader.onload = (function (e) {
            previewSrc.value[i] = e.target.result;
        });
        fileReader.readAsDataURL(form.images[i]);
    }
};

function favorite (favoritedPost) {
    favoritedPost.favorited_by_user = !favoritedPost.favorited_by_user;
    axios.post('/post/' + favoritedPost.id + '/favorites')
    .then((response) => {
        console.log(response.data);
    });
};

// 投稿者のページに遷移
// 投稿者が自分か他人かで分岐
function toMyPage (userId) {
    if (userId != props.user.id) {
        Inertia.get('/mycloset/' + userId);
    } else {
        Inertia.get('/mycloset');
    }
};

function index () {
    Inertia.get('/dashboard/followed');
};

function recomend () {
    Inertia.get('/dashboard');
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">タイムライン</h2>
        </template>
        
        <div class="bg-white h-screen">
            <div class="mx-auto max-w-2xl py-1 px-4 sm:py-1 sm:px-6 lg:max-w-7xl lg:px-8">
                
                <div class="flex justify-center items-center  h-11 w-full border border-t-0 border-r-0 border-l-0 border-b-2 border-gray-200">
                    <div class="flex justify-center items-center">
                        <button @click="recomend" class="p-2 text-base font-medium text-gray-500">おすすめ</button>
                        <button @click="index" class="p-2 text-base font-medium text-gray-500 border boeder-t-2 border-r-2 border-l-2 border-b-0">フォロー中</button>
                    </div>
                </div>
                
                <div v-if="pleaseFollow">
                    <div class="flex justify-center items-center w-full h-4/5">
                        <div class="w-80 h-80 m-20">
                            <FontAwesomeIcon class="w-40 h-40 justify-self-center mx-20" icon="user-plus"></FontAwesomeIcon>
                            <h1 class="text-2xl text-black mx-7">誰かをフォローしてみる</h1>
                            <button @click="recomend" class="text-base text-blue-600 mx-24 my-2">おすすめへ移動</button>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                        <!-- 洋服要素 -->
                        <div v-for="(post, index) in posts" :key="post.name" class="group relative">
                            <!-- 写真要素 -->
                            <div class="min-h-80 aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none lg:h-80">
                                <img :src="post.images[0].image_url" class="h-full w-full object-cover object-center lg:h-full lg:w-full">
                            </div>
                            <!-- 商品名など -->
                            <div class="mt-1 flex">
                                <div class="reactive w-1/4">
                                    <img :src="post.user.profile_image_url" class="absolute z-0 w-10 h-10 rounded-full m-2">
                                    <button @click="toMyPage(post.user.id)" class="absolute z-10 w-10 h-10 rounded-full m-2"></button>
                                </div>
                                <div class="relative w-2/4">
                                    <h2 class="mt-2 text-sm text-gray-600">{{ post.user.name }}</h2>
                                    <h3 class="text-xs text-gray-500">
                                        <a @click="openDescription(post)">
                                            <span aria-hidden="true" class="absolute inset-0"></span>
                                            {{ post.title }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="relative w-1/4">
                                    <div v-if="post.favorited_by_user">
                                        <img class="absolute z-0 w-5 h-5 m-5" src="https://res.cloudinary.com/dxe6weecx/image/upload/v1673066664/%E3%83%8F%E3%83%BC%E3%83%88%E3%81%AE%E3%83%9E%E3%83%BC%E3%82%AF_f6qjah.png">
                                    </div>
                                    <div v-else>
                                        <img class="absolute z-0 w-5 h-5 m-5" src="https://res.cloudinary.com/dxe6weecx/image/upload/v1673013731/%E3%83%8F%E3%83%BC%E3%83%88%E3%81%AE%E3%83%9E%E3%83%BC%E3%82%AF2_iw22ho.png">
                                    </div>
                                    <button @click="favorite(post)" class="absolute z-10 w-5 h-5 m-5"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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
                                                <img :src="url" class="h-full w-full object-cover object-center"/>
                                            </swiperSlide>
                                        </swiper>
                                    </div>
                                    <div v-else>
                                        <img src='https://res.cloudinary.com/dxe6weecx/image/upload/v1671761587/%E5%86%99%E7%9C%9F%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B34_cughdj.png' class="h-full w-full object-cover object-center">
                                    </div>
                                </div>
                                <!-- フォーム部分 -->
                                <div class="h-full mb-4 sm:col-span-4 lg:col-span-5">
                                    <form @submit.prevent="submit" enctype="multipart/form-data">
                                        <div class="overflow-hidden sm:rounded-md">
                                            <div class="bg-white px-4 py-5 sm:p-6">
                                                <div class="">
                                                    <label for="post_title" class="block text-sm font-medium text-gray-700">タイトル</label>
                                                    <input v-model="form.title" type="text" name="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                                <div class="">
                                                    <label for="post_body" class="block text-sm font-medium text-gray-700">説明</label>
                                                    <input v-model="form.body" type="text" name="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                                <div class="">
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
    </AuthenticatedLayout>
</template>