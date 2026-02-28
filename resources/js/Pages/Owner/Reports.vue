<template>
    <OwnerLayout :stores="stores" :current-store="currentStore">
        <div class="space-y-4 md:space-y-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Financial Reports</h1>

            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input
                                v-model="startDate"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input
                                v-model="endDate"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                        <button
                            @click="loadReport"
                            class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            Load Report
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <SummaryStatCard label="Income" :value="reportData.total_income" type="income" />
                        <SummaryStatCard label="Expense" :value="reportData.total_expense" type="expense" />
                        <SummaryStatCard label="Profit" :value="reportData.profit" type="profit" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Transaction Details</h2>
                </div>
                
                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-gray-200">
                    <div v-for="transaction in transactions" :key="transaction.id" class="p-4 space-y-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ formatDate(transaction.transaction_date) }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ transaction.customer?.name || '-' }}</div>
                            </div>
                            <span :class="transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs font-semibold rounded-full">
                                {{ transaction.type }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500" v-if="transaction.category">{{ transaction.category }}</div>
                        <div class="text-xs text-gray-500" v-if="transaction.note">{{ transaction.note }}</div>
                        <div class="text-right text-base font-semibold" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                            {{ formatCurrency(transaction.amount) }}
                        </div>
                    </div>
                    <div v-if="transactions.length === 0" class="p-8 text-center text-gray-500">
                        No transactions found
                    </div>
                </div>

                <!-- Desktop View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in transactions" :key="transaction.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(transaction.transaction_date) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ transaction.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.customer?.name || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.category || '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.note || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                    {{ formatCurrency(transaction.amount) }}
                                </td>
                            </tr>
                            <tr v-if="transactions.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No transactions found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import OwnerLayout from '../../Layouts/OwnerLayout.vue';
import SummaryStatCard from '../../Components/SummaryStatCard.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    stores: { type: Array, default: () => [] },
    currentStore: Object,
    reportData: { type: Object, default: () => ({ total_income: 0, total_expense: 0, profit: 0 }) },
    transactions: { type: Array, default: () => [] },
    startDate: { type: String, default: () => new Date().toISOString().split('T')[0] },
    endDate: { type: String, default: () => new Date().toISOString().split('T')[0] },
});

const startDate = ref(props.startDate);
const endDate = ref(props.endDate);

const today = computed(() => new Date().toISOString().split('T')[0]);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
};

const loadReport = () => {
    if (props.currentStore) {
        router.reload({ 
            data: { 
                start_date: startDate.value,
                end_date: endDate.value
            } 
        });
    }
};
</script>
