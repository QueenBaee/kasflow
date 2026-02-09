<template>
    <CashierLayout :store-name="store.name">
        <div class="max-w-2xl mx-auto space-y-6">
            <!-- Today's Income Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <p class="text-sm font-medium text-gray-600 mb-2">Today's Total Income</p>
                <p class="text-5xl font-bold text-green-600 mb-4">
                    {{ formatCurrency(todayIncome) }}
                </p>
                <p class="text-sm text-gray-500">{{ transactionCount }} transactions</p>
            </div>

            <!-- Quick Action Button -->
            <Link
                href="/cashier/income"
                class="block w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-2xl shadow-lg p-8 text-center transition-all transform hover:scale-105"
            >
                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <p class="text-2xl font-bold">Record Income</p>
                <p class="text-sm opacity-90 mt-2">Tap to add new sale</p>
            </Link>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Transactions</h3>
                <div v-if="recentTransactions.length > 0" class="space-y-3">
                    <div
                        v-for="transaction in recentTransactions"
                        :key="transaction.id"
                        class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0"
                    >
                        <div>
                            <p class="font-medium text-gray-900">{{ formatCurrency(transaction.amount) }}</p>
                            <p class="text-sm text-gray-500">{{ formatTime(transaction.created_at) }}</p>
                        </div>
                        <div class="text-right">
                            <p v-if="transaction.note" class="text-sm text-gray-600">{{ transaction.note }}</p>
                        </div>
                    </div>
                </div>
                <p v-else class="text-center text-gray-500 py-8">No transactions yet today</p>
            </div>
        </div>
    </CashierLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import CashierLayout from '../../Layouts/CashierLayout.vue';
import { formatCurrency } from '../../Utils/currency';

const props = defineProps({
    store: { type: Object, required: true },
    todayIncome: { type: Number, default: 0 },
    transactionCount: { type: Number, default: 0 },
    recentTransactions: { type: Array, default: () => [] },
});

const formatTime = (datetime) => {
    return new Date(datetime).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
