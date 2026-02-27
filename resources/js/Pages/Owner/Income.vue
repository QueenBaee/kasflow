<template>
    <OwnerLayout>
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Record Income</h2>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                        <select v-model="form.customer_id" @change="onCustomerChange" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option :value="null">-- Select Customer --</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                {{ customer.name }} - {{ customer.speed_package }} ({{ formatCurrency(customer.monthly_fee) }})
                            </option>
                        </select>
                    </div>

                    <CurrencyInput
                        v-model="form.amount"
                        label="Amount"
                        :error="form.errors.amount"
                        id="amount"
                        ref="amountInput"
                        :disabled="true"
                    />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note (Optional)</label>
                        <textarea
                            v-model="form.note"
                            rows="3"
                            placeholder="Add a note..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                    </div>

                    <div class="flex gap-3">
                        <BaseButton
                            type="submit"
                            variant="success"
                            :loading="form.processing"
                            :disabled="!form.customer_id || !form.amount || form.amount <= 0"
                            class="flex-1"
                        >
                            Save Income
                        </BaseButton>

                        <Link
                            :href="route('dashboard')"
                            class="px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import OwnerLayout from '../../Layouts/OwnerLayout.vue';
import CurrencyInput from '../../Components/CurrencyInput.vue';
import BaseButton from '../../Components/BaseButton.vue';

const props = defineProps({
    stores: { type: Array, required: true },
    currentStore: { type: Object, default: null },
    customers: { type: Array, default: () => [] },
});

const form = useForm({
    customer_id: null,
    amount: 0,
    note: '',
});

const amountInput = ref(null);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const onCustomerChange = () => {
    if (form.customer_id) {
        const customer = props.customers.find(c => c.id === form.customer_id);
        if (customer) {
            form.amount = customer.monthly_fee;
        }
    } else {
        form.amount = 0;
    }
};

const handleSubmit = () => {
    if (!props.currentStore) {
        alert('Please select a store first');
        return;
    }

    form.post(route('transactions.income.store', props.currentStore.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>
