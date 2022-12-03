<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from "@inertiajs/inertia";
import { ref, reactive } from 'vue';

// IteμControllerから受け取ったデータ
const props = defineProps ({
    items: Array,
    types: Array,
    categories: Array,
    brands: Array,
});

// 入力された洋服情報を保持する変数
const form = reactive({
    name: null,
    type_id: null,
    categorie_id: null,
    brand_id: null,
    price: null,
    possessioned: null,
});

// 洋服追加画面の表示非表示を制御する
const showContent = ref(false);

function openModal () {
    showContent.value = true
};

function closeModal () {
    showContent.value = false;
};

// "/mycloset"にPOSTメソッドでitemsテーブルに追加する洋服情報を送信"
function submit() {
    Inertia.post("/mycloset", form);
}
</script>

<template>
    <Head title="MyCloset" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My closet</h2>
        </template>
        
        <div className="h-screen grid grid-cols-10">
            <div className="bg-white col-start-1 col-span-2">
                <PrimaryButton @click="openModal">洋服を追加</PrimaryButton> <!-- このボタンを押すと洋服を追加するためのモーダルが表示される -->
            </div>
            <div className="bg-white col-start-3 col-span-11"> 
                <div v-for="item in items" class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:shadow-slate-700/[.7]">  <!-- このブロックにitemsテーブルから引き出した洋服情報を並べる -->
                    <img class="w-full h-auto rounded-t-xl" src="" alt="Image Description">
                    <div class="p-4 md:p-5">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ item.name }}
                        </h3>
                        <p class="mt-1 text-gray-800 dark:text-gray-400">
                            {{ item.categorie_id }}
                        </p>
                        <p class="mt-1 text-gray-800 dark:text-gray-400">
                            {{ item.brand_id }}
                        </p>
                        <p class="mt-1 text-gray-800 dark:text-gray-400">
                            {{ item.price }}円
                        </p>
                        <a class="mt-3 py-3 px-4 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm dark:focus:ring-offset-gray-800" href="#">
                          詳細を見る
                        </a>
                    </div>
                </div>
                <div v-show="showContent" class="h-100 w-200 flex justify-center items-center bg-white">  <!-- 追加する洋服情報を入力するためのモーダル -->
                    <div class="h-full w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="h-full w-full bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <form @submit.prevent="submit">
                                <div>
                                    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">商品名</label>
                                    <input v-model="form.name" type="text" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="商品名を入力" required>
                                </div>
                                <div>
                                    <p>種類を選択</p>
                                    <label for="underline_select" class="sr-only">Underline select</label>
                                    <select v-model="form.type_id" id="underline_select" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                        <option v-for="type in types" :key="type.name" :value="type.id">{{ type.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <p>カテゴリーを選択</p>
                                    <label for="underline_select" class="sr-only">Underline select</label>
                                    <select v-model="form.categorie_id" id="underline_select" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                        <option v-for="categorie in categories" :key="categorie.name" :value="categorie.id">{{ categorie.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <p>ブランドを選択</p>
                                    <label for="underline_select" class="sr-only">Underline select</label>
                                    <select v-model="form.brand_id" id="underline_select" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                        <option v-for="brand in brands" :key="brand.name" :value="brand.id">{{ brand.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">金額を入力</label>
                                    <input v-model="form.price" type="number" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="金額を入力" required>
                                </div>
                                <div>
                                    <p>所持品ですか？</p>
                                    <label for="underline_select" class="sr-only">Underline select</label>
                                    <select v-model="form.possessioned" id="underline_select" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                        <option :value="true">所持品</option>
                                        <option :value="false">購入検討中</option>
                                    </select>
                                </div>
                                <PrimaryButton @click="closeModal">追加</PrimaryButton>  <!-- クリックでモーダルを閉じ洋服情報を送信 -->
                            </form>
                        </div>    
                    </div>     
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>