<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    auth: Object,
    members: Array,
    roles: Array,
});

const showDeleteModal = ref(false);
const memberToDelete = ref(null);

const roleLabel = (value) => {
    const role = props.roles.find(r => r.value === value);
    return role ? role.label : value;
};

const confirmDelete = (member) => {
    memberToDelete.value = member;
    showDeleteModal.value = true;
};

const deleteMember = () => {
    if (memberToDelete.value) {
        router.delete(`/admin/members/${memberToDelete.value.id}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                memberToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="メンバー管理" />

    <AuthLayout>
        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                        メンバー管理
                    </h1>
                    <Link
                        href="/admin/members/create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
                    >
                        + 新規メンバー
                    </Link>
                </div>

                <!-- メンバー一覧 -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    名前
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    メールアドレス
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    権限
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    操作
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="member in members" :key="member.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ member.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ member.email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-purple-100 text-purple-800': member.role === 'admin',
                                            'bg-blue-100 text-blue-800': member.role === 'recruiter',
                                            'bg-green-100 text-green-800': member.role === 'interviewer',
                                            'bg-gray-100 text-gray-800': member.role === 'viewer',
                                        }"
                                    >
                                        {{ roleLabel(member.role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="`/admin/members/${member.id}/edit`"
                                        class="text-blue-600 hover:text-blue-900 mr-4"
                                    >
                                        編集
                                    </Link>
                                    <button
                                        @click="confirmDelete(member)"
                                        class="text-red-600 hover:text-red-900"
                                        :disabled="member.user_id === auth.user.id"
                                        :class="{ 'opacity-50 cursor-not-allowed': member.user_id === auth.user.id }"
                                    >
                                        削除
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="members.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    メンバーがいません
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 戻るリンク -->
                <div class="mt-6">
                    <Link href="/" class="text-sm text-blue-600 hover:underline">
                        &larr; トップページに戻る
                    </Link>
                </div>
            </div>
        </div>

        <!-- 削除確認モーダル -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    メンバーを削除しますか？
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    {{ memberToDelete?.name }} さんをメンバーから削除します。この操作は取り消せません。
                </p>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        キャンセル
                    </button>
                    <button
                        @click="deleteMember"
                        class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition"
                    >
                        削除する
                    </button>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
