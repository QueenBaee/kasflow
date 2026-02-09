<template>
    <OwnerLayout :stores="stores" :current-store="currentStore">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Cashier Management</h1>
                <BaseButton variant="primary" size="md" @click="showModal = true">
                    Add Cashier
                </BaseButton>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div v-if="cashiers.length > 0" class="space-y-4">
                        <div
                            v-for="cashier in cashiers"
                            :key="cashier.id"
                            class="flex items-center justify-between p-4 border border-gray-200 rounded-lg"
                        >
                            <div>
                                <p class="font-semibold text-gray-900">{{ cashier.name }}</p>
                                <p class="text-sm text-gray-500">{{ cashier.email }}</p>
                            </div>
                            <BaseButton
                                variant="danger"
                                size="sm"
                                @click="removeCashier(cashier.id)"
                                :loading="removing === cashier.id"
                            >
                                Remove
                            </BaseButton>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 text-center py-12">
                        No cashiers assigned yet
                    </p>
                </div>
            </div>
        </div>

        <!-- Add Cashier Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-gray-900 bg-opacity-20 flex items-center justify-center z-50 p-4"
            @click.self="showModal = false"
        >
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Add Cashier</h3>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Cashier Email
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="cashier@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        <p class="mt-1 text-xs text-gray-500">Enter the email of an existing user</p>
                    </div>

                    <div class="flex gap-3">
                        <BaseButton
                            type="submit"
                            variant="primary"
                            size="md"
                            :loading="form.processing"
                            class="flex-1"
                        >
                            Add Cashier
                        </BaseButton>
                        <BaseButton
                            type="button"
                            variant="secondary"
                            size="md"
                            @click="showModal = false"
                            class="flex-1"
                        >
                            Cancel
                        </BaseButton>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import OwnerLayout from '../../Layouts/OwnerLayout.vue';
import BaseButton from '../../Components/BaseButton.vue';

const props = defineProps({
    stores: { type: Array, default: () => [] },
    currentStore: Object,
    cashiers: { type: Array, default: () => [] },
});

const showModal = ref(false);
const removing = ref(null);

const form = useForm({
    email: '',
});

const handleSubmit = () => {
    if (!props.currentStore) {
        alert('Please select a store first');
        return;
    }

    form.post(route('cashiers.assign', props.currentStore.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showModal.value = false;
        },
    });
};

const removeCashier = (userId) => {
    if (!confirm('Are you sure you want to remove this cashier?')) return;
    
    removing.value = userId;
    router.delete(route('cashiers.remove', { store: props.currentStore.id, user: userId }), {
        preserveScroll: true,
        onFinish: () => {
            removing.value = null;
        },
    });
};
</script>
