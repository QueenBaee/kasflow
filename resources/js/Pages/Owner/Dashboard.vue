<template>
    <OwnerLayout :stores="stores" :current-store="currentStore">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">{{ currentDate }}</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <SummaryStatCard
                    label="Today's Income"
                    :value="summary.todayIncome"
                    type="income"
                    subtitle="Total sales today"
                />
                <SummaryStatCard
                    label="Today's Expense"
                    :value="summary.todayExpense"
                    type="expense"
                    subtitle="Total costs today"
                />
                <SummaryStatCard
                    label="Today's Profit"
                    :value="summary.todayProfit"
                    type="profit"
                    :subtitle="summary.todayProfit >= 0 ? 'Positive' : 'Negative'"
                />
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Link
                    :href="route('expenses.index')"
                    class="bg-white hover:bg-gray-50 border-2 border-gray-200 rounded-lg p-6 flex items-center transition-colors">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Record Expense</p>
                        <p class="text-sm text-gray-600">Add new expense</p>
                    </div>
                </Link>

                <Link
                    :href="route('reports.index')"
                    class="bg-white hover:bg-gray-50 border-2 border-gray-200 rounded-lg p-6 flex items-center transition-colors">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">View Reports</p>
                        <p class="text-sm text-gray-600">Financial analysis</p>
                    </div>
                </Link>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <div
                        v-for="transaction in recentTransactions"
                        :key="transaction.id"
                        class="px-6 py-4 flex items-center justify-between hover:bg-gray-50"
                    >
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center mr-4"
                                :class="transaction.type === 'income' ? 'bg-green-100' : 'bg-red-100'"
                            >
                                <svg
                                    class="w-5 h-5"
                                    :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        v-if="transaction.type === 'income'"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 11l5-5m0 0l5 5m-5-5v12"
                                    />
                                    <path
                                        v-else
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 13l-5 5m0 0l-5-5m5 5V6"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ transaction.type === 'income' ? 'Income' : 'Expense' }}
                                    <span v-if="transaction.category" class="text-gray-500">- {{ transaction.category }}</span>
                                </p>
                                <p class="text-sm text-gray-500">{{ formatDateTime(transaction.created_at) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p
                                class="font-semibold"
                                :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                            >
                                {{ transaction.type === 'income' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                            </p>
                            <p v-if="transaction.note" class="text-sm text-gray-500">{{ transaction.note }}</p>
                        </div>
                    </div>
                    <div v-if="recentTransactions.length === 0" class="px-6 py-12 text-center text-gray-500">
                        No transactions yet
                    </div>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import OwnerLayout from '../../Layouts/OwnerLayout.vue';
import SummaryStatCard from '../../Components/SummaryStatCard.vue';
import { formatCurrency } from '../../Utils/currency';

const props = defineProps({
    stores: { type: Array, default: () => [] },
    currentStore: Object,
    summary: {
        type: Object,
        default: () => ({
            todayIncome: 0,
            todayExpense: 0,
            todayProfit: 0,
        }),
    },
    recentTransactions: { type: Array, default: () => [] },
});

const currentDate = computed(() => {
    return new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
