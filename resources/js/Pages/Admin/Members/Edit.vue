<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    auth: Object,
    member: Object,
    roles: Array,
});

const form = useForm({
    role: props.member.role,
});

const submit = () => {
    form.put(`/admin/members/${props.member.id}`, {
        preserveScroll: true,
    });
};

const isSelf = props.member.user_id === props.auth.user.id;
</script>

<template>
    <Head title="権限編集" />

    <AuthLayout>
        <div class="py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    権限編集
                </h1>

                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ member.name }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            {{ member.email }}
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <!-- 自分自身の場合の警告 -->
                        <div v-if="isSelf" class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
                            自分自身の権限は変更できません
                        </div>

                        <!-- 権限 -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                権限
                            </label>
                            <select
                                id="role"
                                v-model="form.role"
                                :disabled="isSelf"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
                            >
                                <option v-for="role in roles" :key="role.value" :value="role.value">
                                    {{ role.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                                {{ form.errors.role }}
                            </p>
                        </div>

                        <!-- 権限の説明 -->
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                            <p class="font-medium text-gray-700 mb-2">権限の説明</p>
                            <ul class="space-y-1">
                                <li><span class="font-medium">管理者:</span> メンバーの追加・削除・権限変更が可能</li>
                                <li><span class="font-medium">採用担当:</span> 求人・応募者の管理が可能</li>
                                <li><span class="font-medium">面接官:</span> 面接・評価の入力が可能</li>
                                <li><span class="font-medium">閲覧者:</span> 閲覧のみ可能</li>
                            </ul>
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
                                :disabled="form.processing || isSelf"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                保存
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
