<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.value.auth.user;

// プレビュー用のURLを格納
const previewSrc=ref(user.profile_image_url);

const form = useForm({
    image: user.profile_image_url,
    name: user.name,
    email: user.email,
    profile: user.profile,
});

// プレビュー用のURLを取得
function uploadFile(event) {
    form.image = event.target.files[0];
    console.log(form.image);
    var fileReader=new FileReader();
    fileReader.onload=()=>{
        previewSrc.value=fileReader.result;
    };
    fileReader.readAsDataURL(event.target.files[0]);
};

const submit = () =>{
    form.post(route('profile.update'));
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">プロフィール情報</h2>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6" enctype="multipart/form-data">
            <div>
                <div class="flex justify-start items-center">
                    <InputLabel for="image" value="プロフィール画像" />
                    <input @change="uploadFile" name="file-upload" type="file" class="block w-80 mx-2 text-sm font-medium text-slate-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-violet-50 file:text-violet-700
                        hover:file:bg-violet-100
                    "/>
                </div>
                
                <img v-if="previewSrc != null" :src="previewSrc" class="rounded-full w-32 h-32 m-2 border border-gray-200 border-2">
                <div v-else class="flex justify-center items-center rounded-full w-32 h-32 m-2 border border-gray-200 border-2">
                    <FontAwesomeIcon icon="user" class="w-2/3 h-2/3"></FontAwesomeIcon>
                </div>
                
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            
            <div>
                <InputLabel for="name" value="ユーザー名" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="email"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            
            <div>
                <InputLabel for="profile" value="自己紹介文" />

                <TextInput
                    id="profile"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.profile"
                    required
                    autofocus
                    autocomplete="profile"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div v-if="props.mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    入力されたメールアドレスは無効です
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="props.status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">保存</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">保存されました</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
