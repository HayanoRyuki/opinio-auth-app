<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({});

const submit = () => {
    form.post('/logout');
};
</script>

<template>
    <Head title="ログアウト" />

    <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md p-8 bg-white shadow-lg rounded-lg border border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                ログアウトしますか？
            </h1>

            <div class="flex space-x-4">
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition disabled:opacity-50"
                >
                    ログアウト
                </button>
                <Link
                    href="/"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded transition text-center"
                >
                    キャンセル
                </Link>
            </div>
        </div>
    </div>
</template>
