<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth?.user;
const isAdmin = page.props.auth?.isAdmin;
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200" v-if="user">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link href="/" class="text-xl font-bold text-gray-900">
                            Opinio Auth
                        </Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ user.email }}</span>
                        <Link
                            v-if="isAdmin"
                            href="/admin/members"
                            class="text-sm text-purple-600 hover:text-purple-800"
                        >
                            メンバー管理
                        </Link>
                        <Link
                            href="/profile"
                            class="text-sm text-blue-600 hover:text-blue-800"
                        >
                            プロフィール
                        </Link>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            ログアウト
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-6">
            <p class="text-center text-gray-400 text-sm">
                &copy; 2025 Opinio Inc. All Rights Reserved.
            </p>
        </footer>
    </div>
</template>
