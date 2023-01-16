<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from "@inertiajs/inertia";
import { ref, reactive, computed, onMounted } from 'vue';
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

// IteμControllerから受け取ったデータ
const props = defineProps ({
    user: Array,
    followees: Number,
    followers: Number,
    items: Array,
    types: Array,
    categories: Array,
    brands: Array,
});

console.log(props.items);

// itemsテーブルに洋服を一つでも追加してればtrue
const hasItems = props.items.length != 0;

// 入力された洋服情報を保持する変数
const form = reactive({
    name: null,
    type_id: null,
    categorie_id: null,
    brand_id: null,
    price: null,
    possessioned: null,
    file: null,
    description: null,
});

// 洋服詳細の表示を制御する
const showDescription = ref(false);

if (hasItems) {
    let selectedItem = ref(props.items[0]);
} else {
    let selectedItem = ref([]);
}

function openDescription (item) {
    selectedItem.value = item;
    showDescription.value = true;
};

function closeDescription () {
    showDescription.value = false;
    
};

// 洋服追加画面の表示を制御する
const showAddForm = ref(false);

function openAddForm () {
    showAddForm.value = true;
};

function closeAddForm () {
    showAddForm.value = false;
};

// "/mycloset"にPOSTメソッドでitemsテーブルに追加する洋服情報を送信"
function submit () {
    Inertia.post("/mycloset", form);
};

// 洋服詳細編集機能
const showUpdateForm = ref(false);

function openUpdateForm () {
    showUpdateForm.value = true;
    form.name = selectedItem.value.name;
    form.type_id = selectedItem.value.type.id;
    form.categorie_id = selectedItem.value.categorie.id;
    form.brand_id = selectedItem.value.brand.id;
    form.price = selectedItem.value.price;
    form.description = selectedItem.value.description;
    closeDescription();
}

function closeUpdateForm () {
    showUpdateForm.value = false;
};

function update () {
    console.log(form);
    Inertia.put("/mycloset/" + selectedItem.value.id, form);
};

// 洋服削除
function deleteItem () {
    closeDescription();
    Inertia.delete("/mycloset/" + selectedItem.value.id);
};

let previewSrc = ref(null);

// 画像ファイルオブジェクトをフォームに格納
function uploadFile(event) {
    form.file = event.target.files[0];
    var fileReader=new FileReader();
    fileReader.onload=()=>{
        previewSrc.value=fileReader.result;
    };
    fileReader.readAsDataURL(event.target.files[0]);
    return form.file;
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
    Inertia.get("/mycloset");
}

function showOutfits () {
    Inertia.get("/myposts");
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
                        <button @click="showItems" class="p-2 text-base font-medium text-gray-500 border boeder-t-2 border-r-2 border-l-2 border-b-0">アイテム</button>
                        <button @click="showOutfits" class="p-2  text-base font-medium text-gray-500">コーデ</button>
                    </div>
                    <PrimaryButton @click="openAddForm" class="absolute right-0">アイテムを追加</PrimaryButton>
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
                                        <a>
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
                            <button @click="openAddForm" class="w-40 text-base text-blue-600 mx-20 my-2">アイテムを追加</button>
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
        </div>
        
        <!-- 洋服追加モーダル -->
        <div v-show="showAddForm" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- スクリーン全体を覆うオーバーレイ要素 -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <!-- オーバーレイ要素に重なる要素 -->
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <!-- モーダルの幅や位置を決める要素 -->
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <!-- フォームとボタン全体 -->
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <button @click="closeAddForm" type="button" class="text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
                            <span class="sr-only">Close</span>
                            <!-- xマーク -->
                            <svg class="absolute top-4 right-4 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <!-- enctype="mulutipart/form-data"とすることでtextやfileなどを同時に入力することができる -->
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="overflow-hidden shadow sm:rounded-md">
                                <!-- フォーム部分 -->
                                <div class="bg-white px-4 py-5 sm:p-6">
                                    <div class="">
                                        <div class="">
                                            <div class="flex justify-start items-center">
                                                <label class="block text-sm font-medium text-gray-700">写真</label>
                                                <input @change="uploadFile" name="file-upload" type="file" class="block w-80 mx-2 text-sm font-medium text-slate-500
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-full file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-violet-50 file:text-violet-700
                                                    hover:file:bg-violet-100
                                                "/>
                                            </div>
                                            
                                            
                                            <div class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 ">
                                                <div v-if="previewSrc != null">
                                                    <img :src="previewSrc" class="h-full w-full object-cover object-center">
                                                </div>
                                                <div v-else>
                                                    <div class="space-y-1 text-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="">
                                            <label for="product_name" class="block text-sm font-medium text-gray-700">商品名</label>
                                            <input v-model="form.name" type="text" name="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                    
                                        <div class="">
                                            <label for="type" class="block text-sm font-medium text-gray-700">タイプ</label>
                                            <select v-model="form.type_id" name="type" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                <option v-for="type in types" :key="type.name" :value="type.id">{{ type.name }}</option>
                                            </select>
                                        </div>
                                  
                                        <div class="">
                                            <label for="categorie" class="block text-sm font-medium text-gray-700">カテゴリー</label>
                                            <select v-model="form.categorie_id" name="categorie" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                              <option v-for="categorie in categories" :key="categorie.name" :value="categorie.id">{{ categorie.name }}</option>
                                            </select>
                                        </div>
                                  
                                        <div class="">
                                            <label for="brand" class="block text-sm font-medium text-gray-700">ブランド</label>
                                            <select v-model="form.brand_id" name="brand" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                <option v-for="brand in brands" :key="brand.name" :value="brand.id">{{ brand.name }}</option>
                                            </select>
                                        </div>
                                  
                                        <div class="">
                                            <label for="price" class="block text-sm font-medium text-gray-700">価格</label>
                                            <input v-model="form.price" type="number" name="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        
                                        <div class="">
                                            <label for="possessioned" class="block text-sm font-medium text-gray-700">所持中?</label>
                                            <select v-model="form.possessioned" name="possessioned" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                <option :value="true">所持品</option>
                                                <option :value="false">購入検討中</option>
                                            </select>
                                        </div>
                                        
                                        <div class="">
                                            <label for="product_description" class="block text-sm font-medium text-gray-700">説明</label>
                                            <input v-model="form.description" type="text" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                                <!-- ボタン部分 -->
                                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                                    <button @click="closeAddForm" type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 洋服編集モーダル -->
        <div v-show="showUpdateForm" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- スクリーン全体を覆うオーバーレイ要素 -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <!-- オーバーレイ要素に重なる要素 -->
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <!-- モーダルの幅や位置を決める要素 -->
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <!-- フォームとボタン全体 -->
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <button @click="closeUpdateForm" type="button" class="text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8">
                            <span class="sr-only">Close</span>
                            <!-- xマーク -->
                            <svg class="absolute top-4 right-4 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <!-- enctype="mulutipart/form-data"とすることでtextやfileなどを同時に入力することができる -->
                        <form @submit.prevent="update" enctype="multipart/form-data">
                            <div class="overflow-hidden shadow sm:rounded-md">
                                <!-- フォーム部分 -->
                                <div class="bg-white px-4 py-5 sm:p-6">
                                    <div class="">
                                        <div class="">
                                            <div class="flex justify-start items-center">
                                                <label class="block text-sm font-medium text-gray-700">写真</label>
                                                <input @change="uploadFile" name="file-upload" type="file" class="block w-80 mx-2 text-sm font-medium text-slate-500
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-full file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-violet-50 file:text-violet-700
                                                    hover:file:bg-violet-100
                                                "/>
                                            </div>
                                            <div class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 ">
                                                <img :src="selectedItem.image_url" class="h-full w-full object-cover object-center">
                                            </div>
                                        </div>
                                
                                        <div class="">
                                            <label for="product_name" class="block text-sm font-medium text-gray-700">商品名</label>
                                            <input v-model="form.name" type="text" name="street-address" :value="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                    
                                        <div class="">
                                            <label for="type" class="block text-sm font-medium text-gray-700">タイプ</label>
                                            <select v-model="form.type_id" name="type" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                <option v-for="type in types" :key="type.name" :value="type.id">{{ type.name }}</option>
                                            </select>
                                        </div>
                                  
                                        <div class="">
                                            <label for="categorie" class="block text-sm font-medium text-gray-700">カテゴリー</label>
                                            <select v-model="form.categorie_id" name="categorie" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                              <option v-for="categorie in categories" :key="categorie.name" :value="categorie.id">{{ categorie.name }}</option>
                                            </select>
                                        </div>
                                  
                                        <div class="">
                                            <label for="brand" class="block text-sm font-medium text-gray-700">ブランド</label>
                                            <select v-model="form.brand_id" name="brand" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                <option v-for="brand in brands" :key="brand.name" :value="brand.id">{{ brand.name }}</option>
                                            </select>
                                        </div>
                                  
                                        <div class="">
                                            <label for="price" class="block text-sm font-medium text-gray-700">価格</label>
                                            <input v-model="form.price" type="number" name="price" :value="form.price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        
                                        <div class="">
                                            <label for="possessioned" class="block text-sm font-medium text-gray-700">持ってる</label>
                                            <select v-model="form.possessioned" name="possessioned" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                <option :value="true">所持品</option>
                                                <option :value="false">購入検討中</option>
                                            </select>
                                        </div>
                                        
                                        <div class="">
                                            <label for="product_description" class="block text-sm font-medium text-gray-700">説明</label>
                                            <input v-model="form.description" type="text" name="description" :value="form.description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                                <!-- ボタン部分 -->
                                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                                    <button @click="closeUpdateForm" type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>