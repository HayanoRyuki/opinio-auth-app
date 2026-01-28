<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ClipboardDocumentListIcon,
    AcademicCapIcon,
    UserGroupIcon,
    ClipboardDocumentCheckIcon,
    GlobeAltIcon,
} from '@heroicons/vue/24/outline';

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

// アプリ一覧
const apps = [
    {
        name: '採用管理',
        nameEn: 'ATS',
        icon: ClipboardDocumentListIcon,
        color: 'bg-blue-500 hover:bg-blue-600',
        available: true,
        url: '/sso/start?client_id=opinio-ats',
    },
    {
        name: 'キャリア',
        nameEn: 'Career',
        icon: AcademicCapIcon,
        color: 'bg-emerald-500',
        available: false,
    },
    {
        name: '面接',
        nameEn: 'Interview',
        icon: UserGroupIcon,
        color: 'bg-purple-500',
        available: false,
    },
    {
        name: 'アンケート',
        nameEn: 'Enquete',
        icon: ClipboardDocumentCheckIcon,
        color: 'bg-orange-500',
        available: false,
    },
    {
        name: '採用サイト',
        nameEn: 'CMS',
        icon: GlobeAltIcon,
        color: 'bg-pink-500',
        available: false,
    },
];
</script>

<template>
    <Head title="Opinio - アプリポータル" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-2xl">
            <!-- ヘッダー -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-slate-800 mb-2">
                    Opinio
                </h1>
                <p class="text-slate-500">
                    採用プラットフォーム
                </p>
            </div>

            <!-- ログイン済み -->
            <template v-if="auth?.user">
                <div class="bg-white shadow-xl rounded-2xl p-4 sm:p-8 border border-slate-200">
                    <!-- ユーザー情報 -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 pb-4 border-b border-slate-100">
                        <div class="mb-3 sm:mb-0">
                            <p class="text-xs text-slate-500">ログイン中</p>
                            <p class="font-medium text-slate-800 text-sm sm:text-base truncate">{{ auth.user.email }}</p>
                        </div>
                        <div class="flex items-center space-x-3 text-xs sm:text-sm">
                            <Link
                                v-if="auth?.isAdmin"
                                href="/admin/members"
                                class="text-purple-600 hover:text-purple-800 font-medium whitespace-nowrap"
                            >
                                メンバー管理
                            </Link>
                            <Link
                                href="/profile"
                                class="text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap"
                            >
                                プロフィール
                            </Link>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="text-slate-500 hover:text-slate-700 whitespace-nowrap"
                            >
                                ログアウト
                            </Link>
                        </div>
                    </div>

                    <!-- アプリ一覧 -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <template v-for="app in apps" :key="app.nameEn">
                            <!-- 有効なアプリ -->
                            <a
                                v-if="app.available"
                                :href="app.url"
                                :class="[
                                    'flex flex-col items-center justify-center p-6 rounded-xl text-white transition-all duration-200 transform hover:scale-105 hover:shadow-lg',
                                    app.color
                                ]"
                            >
                                <component :is="app.icon" class="w-10 h-10 mb-3" />
                                <span class="font-bold text-lg">{{ app.name }}</span>
                                <span class="text-sm opacity-80">{{ app.nameEn }}</span>
                            </a>

                            <!-- 無効なアプリ（Coming Soon） -->
                            <div
                                v-else
                                :class="[
                                    'flex flex-col items-center justify-center p-6 rounded-xl text-white opacity-50 cursor-not-allowed',
                                    app.color
                                ]"
                            >
                                <component :is="app.icon" class="w-10 h-10 mb-3" />
                                <span class="font-bold text-lg">{{ app.name }}</span>
                                <span class="text-xs opacity-80">Coming Soon</span>
                            </div>
                        </template>
                    </div>

                    <!-- 管理者メニュー -->
                    <div v-if="auth?.isAdmin" class="mt-6 pt-6 border-t border-slate-100">
                        <p class="text-sm text-slate-500 mb-3">管理者メニュー</p>
                        <Link
                            href="/admin/members"
                            class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-lg transition"
                        >
                            <UserGroupIcon class="w-5 h-5 mr-2" />
                            メンバー管理
                        </Link>
                    </div>
                </div>
            </template>

            <!-- 未ログイン -->
            <template v-else>
                <div class="bg-white shadow-xl rounded-2xl p-4 sm:p-8 border border-slate-200">
                    <h2 class="text-lg sm:text-xl font-bold text-center text-slate-800 mb-6">
                        ログイン
                    </h2>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label for="email" class="block text-slate-700 font-medium mb-1">
                                メールアドレス
                            </label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                required
                                class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label for="password" class="block text-slate-700 font-medium mb-1">
                                パスワード
                            </label>
                            <input
                                type="password"
                                id="password"
                                v-model="form.password"
                                required
                                class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50"
                        >
                            ログイン
                        </button>
                    </form>
                </div>
            </template>

            <!-- フッター -->
            <p class="mt-8 text-center text-slate-400 text-sm">
                &copy; 2025 Opinio Inc. All Rights Reserved.
            </p>
        </div>
    </div>
</template>
