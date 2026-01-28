<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    auth: Object,
    status: String,
});

const page = usePage();

// プロフィール更新フォーム
const profileForm = useForm({
    name: props.auth?.user?.name || '',
    email: props.auth?.user?.email || '',
});

// パスワード更新フォーム
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const showPasswordSuccess = ref(false);
const showProfileSuccess = ref(false);

const updateProfile = () => {
    profileForm.put('/profile', {
        preserveScroll: true,
        onSuccess: () => {
            showProfileSuccess.value = true;
            setTimeout(() => showProfileSuccess.value = false, 3000);
        },
    });
};

const updatePassword = () => {
    passwordForm.put('/profile/password', {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showPasswordSuccess.value = true;
            setTimeout(() => showPasswordSuccess.value = false, 3000);
        },
    });
};
</script>

<template>
    <Head title="プロフィール編集" />

    <AuthLayout>
        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    プロフィール編集
                </h1>

                <!-- プロフィール情報カード -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            基本情報
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            アカウントの名前とメールアドレスを変更できます
                        </p>
                    </div>

                    <form @submit.prevent="updateProfile" class="p-6 space-y-4">
                        <!-- 成功メッセージ -->
                        <div
                            v-if="showProfileSuccess"
                            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded"
                        >
                            プロフィールを更新しました
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                名前
                            </label>
                            <input
                                type="text"
                                id="name"
                                v-model="profileForm.name"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">
                                {{ profileForm.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                メールアドレス
                            </label>
                            <input
                                type="email"
                                id="email"
                                v-model="profileForm.email"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">
                                {{ profileForm.errors.email }}
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="profileForm.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition disabled:opacity-50"
                            >
                                保存
                            </button>
                        </div>
                    </form>
                </div>

                <!-- パスワード変更カード -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            パスワード変更
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            セキュリティのため、長くランダムなパスワードを使用してください
                        </p>
                    </div>

                    <form @submit.prevent="updatePassword" class="p-6 space-y-4">
                        <!-- 成功メッセージ -->
                        <div
                            v-if="showPasswordSuccess"
                            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded"
                        >
                            パスワードを更新しました
                        </div>

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                現在のパスワード
                            </label>
                            <input
                                type="password"
                                id="current_password"
                                v-model="passwordForm.current_password"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.current_password }}
                            </p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                新しいパスワード
                            </label>
                            <input
                                type="password"
                                id="password"
                                v-model="passwordForm.password"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.password }}
                            </p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                新しいパスワード（確認）
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                v-model="passwordForm.password_confirmation"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="passwordForm.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.password_confirmation }}
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition disabled:opacity-50"
                            >
                                パスワードを変更
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 戻るリンク -->
                <div class="mt-6">
                    <a href="/" class="text-sm text-blue-600 hover:underline">
                        &larr; トップページに戻る
                    </a>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
