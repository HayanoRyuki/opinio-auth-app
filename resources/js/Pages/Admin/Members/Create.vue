<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    auth: Object,
    roles: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'viewer',
});

const submit = () => {
    form.post('/admin/members', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="新規メンバー作成" />

    <AuthLayout>
        <div class="py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    新規メンバー作成
                </h1>

                <div class="bg-white shadow rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <!-- 名前 -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                名前 <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="山田 太郎"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- メールアドレス -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                メールアドレス <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="example@company.com"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- パスワード -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                パスワード <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="password"
                                v-model="form.password"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="8文字以上"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- パスワード確認 -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                パスワード（確認） <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>

                        <!-- 権限 -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                権限 <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="role"
                                v-model="form.role"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option v-for="role in roles" :key="role.value" :value="role.value">
                                    {{ role.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                                {{ form.errors.role }}
                            </p>
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <Link
                                href="/admin/members"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                            >
                                キャンセル
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition disabled:opacity-50"
                            >
                                作成
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 戻るリンク -->
                <div class="mt-6">
                    <Link href="/admin/members" class="text-sm text-blue-600 hover:underline">
                        &larr; メンバー一覧に戻る
                    </Link>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
