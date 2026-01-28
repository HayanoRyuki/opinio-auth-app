<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    auth: Object,
    ssoLinks: Array,
});

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Opinio 共通認証" />

    <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-lg p-8 bg-white shadow-lg rounded-lg border border-gray-200">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
                Opinio 共通認証
            </h1>

            <!-- ログイン済み -->
            <template v-if="auth?.user">
                <p class="text-center text-gray-700 mb-6">
                    ログイン済み:
                    <span class="font-medium">{{ auth.user.email }}</span>
                </p>

                <div class="space-y-4">
                    <a
                        v-for="link in ssoLinks"
                        :key="link.name"
                        :href="link.url"
                        :class="[
                            'block text-center font-semibold py-2 rounded transition',
                            link.color
                        ]"
                    >
                        {{ link.name }}
                    </a>
                </div>

                <div class="mt-6 flex justify-center space-x-4">
                    <Link
                        v-if="auth?.isAdmin"
                        href="/admin/members"
                        class="text-sm text-purple-600 hover:underline"
                    >
                        メンバー管理
                    </Link>
                    <Link
                        href="/profile"
                        class="text-sm text-blue-600 hover:underline"
                    >
                        プロフィール編集
                    </Link>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="text-sm text-gray-500 hover:underline"
                    >
                        ログアウト
                    </Link>
                </div>
            </template>

            <!-- 未ログイン -->
            <template v-else>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-1">
                            メールアドレス
                        </label>
                        <input
                            type="email"
                            id="email"
                            v-model="form.email"
                            required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label for="password" class="block text-gray-700 font-medium mb-1">
                            パスワード
                        </label>
                        <input
                            type="password"
                            id="password"
                            v-model="form.password"
                            required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded transition disabled:opacity-50"
                    >
                        ログイン
                    </button>
                </form>
            </template>

            <p class="mt-6 text-center text-gray-400 text-sm">
                &copy; 2025 Opinio Inc. All Rights Reserved.
            </p>
        </div>
    </div>
</template>
