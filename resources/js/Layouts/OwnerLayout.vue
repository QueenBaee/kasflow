<template>
    <div class="min-h-screen bg-gray-50">
        <Toast />
        
        <!-- Mobile Header -->
        <div class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md text-gray-600 hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-lg font-bold text-gray-900">Kasflow</h1>
            <div class="w-10"></div>
        </div>

        <!-- Sidebar -->
        <div
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <div class="px-6 py-5 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-blue-600">Kasflow</h1>
                    <p class="text-xs text-gray-500 mt-1">ISP Management</p>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                        :class="isActive(item.href) ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100'"
                    >
                        <span v-html="item.icon" class="w-5 h-5 mr-3"></span>
                        {{ item.name }}
                    </Link>
                </nav>

                <!-- User Menu -->
                <div class="px-4 py-4 border-t border-gray-200">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">{{ userInitials }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                            <p class="text-xs text-gray-500">{{ user.email }}</p>
                        </div>
                    </div>
                    <button
                        @click="handleLogout"
                        class="w-full px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
        ></div>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <main class="p-4 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useAuth } from '../Composables/useAuth';
import Toast from '../Components/Toast.vue';

const props = defineProps({
    stores: { type: Array, default: () => [] },
    currentStore: Object,
});

const { user, logout } = useAuth();
const page = usePage();
const sidebarOpen = ref(false);
const selectedStore = ref(props.currentStore?.id || '');

const userInitials = computed(() => {
    return user.value?.name?.split(' ').map(n => n[0]).join('').toUpperCase() || 'U';
});

const navigation = [
    {
        name: 'Dashboard',
        href: '/dashboard',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
    },
    {
        name: 'Income',
        href: '/income',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    },
    {
        name: 'Expenses',
        href: '/expenses',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
    },
    {
        name: 'Reports',
        href: '/reports',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
    },
    {
        name: 'Customers',
        href: '/customers',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
    },
];

const isActive = (href) => {
    return page.url.startsWith(href);
};

const handleStoreChange = () => {
    if (selectedStore.value) {
        router.reload({
            data: { store_id: selectedStore.value },
            preserveState: false,
        });
    }
};

const handleLogout = () => {
    router.post('/logout');
};
</script>
