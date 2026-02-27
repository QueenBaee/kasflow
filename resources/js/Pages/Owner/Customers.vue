<template>
    <OwnerLayout>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
                <BaseButton @click="showAddModal = true" variant="primary">
                    + Add Customer
                </BaseButton>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Speed Package</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monthly Fee</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="customer in customers" :key="customer.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ customer.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ customer.phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ customer.speed_package }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(customer.monthly_fee) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button @click="editCustomer(customer)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                <button @click="deleteCustomer(customer)" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="customers.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No customers yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showAddModal || editingCustomer" class="fixed inset-0 bg-gray-900 bg-opacity-20 flex items-center justify-center z-50" @click.self="closeModal">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">{{ editingCustomer ? 'Edit Customer' : 'Add Customer' }}</h2>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input v-model="form.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input v-model="form.phone" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Speed Package</label>
                        <input v-model="form.speed_package" type="text" required placeholder="e.g. 10 Mbps" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Fee (Rp)</label>
                        <input v-model="form.monthly_fee" type="number" required min="0" placeholder="e.g. 300000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="flex gap-3 pt-2">
                        <BaseButton type="submit" variant="primary" :loading="form.processing" class="flex-1">
                            {{ editingCustomer ? 'Update' : 'Add' }}
                        </BaseButton>
                        <BaseButton type="button" @click="closeModal" variant="secondary" class="flex-1">
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

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const props = defineProps({
    stores: { type: Array, required: true },
    currentStore: { type: Object, default: null },
    customers: { type: Array, default: () => [] },
});

const showAddModal = ref(false);
const editingCustomer = ref(null);

const form = useForm({
    name: '',
    phone: '',
    speed_package: '',
    monthly_fee: 0,
});

const editCustomer = (customer) => {
    editingCustomer.value = customer;
    form.name = customer.name;
    form.phone = customer.phone;
    form.speed_package = customer.speed_package;
    form.monthly_fee = customer.monthly_fee;
};

const closeModal = () => {
    showAddModal.value = false;
    editingCustomer.value = null;
    form.reset();
};

const submitForm = () => {
    if (!props.currentStore) {
        alert('Please select a store first');
        return;
    }

    if (editingCustomer.value) {
        form.put(route('customers.update', [props.currentStore.id, editingCustomer.value.id]), {
            preserveScroll: true,
            onSuccess: closeModal,
        });
    } else {
        form.post(route('customers.store', props.currentStore.id), {
            preserveScroll: true,
            onSuccess: closeModal,
        });
    }
};

const deleteCustomer = (customer) => {
    if (confirm(`Delete ${customer.name}?`)) {
        router.delete(route('customers.destroy', [props.currentStore.id, customer.id]), {
            preserveScroll: true,
        });
    }
};
</script>
