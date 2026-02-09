<template>
    <OwnerLayout :stores="stores" :current-store="currentStore">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">My Stores</h1>
                <BaseButton variant="primary" size="md">
                    Add Store
                </BaseButton>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="store in stores"
                    :key="store.id"
                    class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow"
                >
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ store.name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">Created {{ formatDate(store.created_at) }}</p>
                    <div class="flex gap-2">
                        <BaseButton variant="secondary" size="sm" class="flex-1">
                            Edit
                        </BaseButton>
                        <BaseButton variant="danger" size="sm" class="flex-1">
                            Delete
                        </BaseButton>
                    </div>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import OwnerLayout from '../../Layouts/OwnerLayout.vue';
import BaseButton from '../../Components/BaseButton.vue';

defineProps({
    stores: { type: Array, default: () => [] },
    currentStore: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>
