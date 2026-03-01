<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
        <Toast />
        
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="px-4 py-4 flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-xl font-bold text-gray-900">{{ storeName }}</h1>
                    <p class="text-sm text-gray-500">{{ currentDate }}</p>
                </div>
                <button
                    @click="handleLogout"
                    class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                >
                    Keluar
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-4 pb-24">
            <slot />
        </main>

        <!-- Bottom Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg">
            <div class="grid grid-cols-2 gap-1 p-2">
                <Link
                    href="/cashier"
                    class="flex flex-col items-center justify-center py-4 rounded-lg transition-colors"
                    :class="isActive('/cashier') && !isActive('/cashier/income') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50'"
                >
                    <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-xs font-medium">Beranda</span>
                </Link>
                <Link
                    href="/cashier/income"
                    class="flex flex-col items-center justify-center py-4 rounded-lg transition-colors"
                    :class="isActive('/cashier/income') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50'"
                >
                    <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="text-xs font-medium">Tambah Pemasukan</span>
                </Link>
            </div>
        </nav>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Toast from '../Components/Toast.vue';

const props = defineProps({
    storeName: { type: String, required: true },
});

const page = usePage();

const currentDate = computed(() => {
    return new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});

const isActive = (href) => {
    return page.url === href || page.url.startsWith(href);
};

const handleLogout = () => {
    router.post('/logout');
};
</script>
