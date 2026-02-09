<template>
    <OwnerLayout :stores="stores" :current-store="currentStore">
        <div class="space-y-6">
            <h1 class="text-3xl font-bold text-gray-900">Financial Reports</h1>

            <div class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors"
                            :class="activeTab === tab.id
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        >
                            {{ tab.name }}
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <div v-if="activeTab === 'daily'" class="space-y-4">
                        <input
                            v-model="dailyDate"
                            type="date"
                            :max="today"
                            class="px-4 py-2 border border-gray-300 rounded-lg"
                        />
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <SummaryStatCard label="Income" :value="dailyReport.total_income" type="income" />
                            <SummaryStatCard label="Expense" :value="dailyReport.total_expense" type="expense" />
                            <SummaryStatCard label="Profit" :value="dailyReport.profit" type="profit" />
                        </div>
                    </div>

                    <div v-if="activeTab === 'weekly'" class="space-y-4">
                        <div class="flex gap-4">
                            <input
                                v-model="weeklyStart"
                                type="date"
                                :max="today"
                                class="px-4 py-2 border border-gray-300 rounded-lg"
                            />
                            <input
                                v-model="weeklyEnd"
                                type="date"
                                :max="today"
                                class="px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <SummaryStatCard label="Income" :value="0" type="income" />
                            <SummaryStatCard label="Expense" :value="0" type="expense" />
                            <SummaryStatCard label="Profit" :value="0" type="profit" />
                        </div>
                    </div>

                    <div v-if="activeTab === 'monthly'" class="space-y-4">
                        <div class="flex gap-4">
                            <select v-model="monthlyMonth" class="px-4 py-2 border border-gray-300 rounded-lg">
                                <option v-for="m in 12" :key="m" :value="m">{{ getMonthName(m) }}</option>
                            </select>
                            <select v-model="monthlyYear" class="px-4 py-2 border border-gray-300 rounded-lg">
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <SummaryStatCard label="Income" :value="0" type="income" />
                            <SummaryStatCard label="Expense" :value="0" type="expense" />
                            <SummaryStatCard label="Profit" :value="0" type="profit" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import OwnerLayout from '../../Layouts/OwnerLayout.vue';
import SummaryStatCard from '../../Components/SummaryStatCard.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    stores: { type: Array, default: () => [] },
    currentStore: Object,
    dailyReport: { type: Object, default: () => ({ total_income: 0, total_expense: 0, profit: 0 }) },
});

const activeTab = ref('daily');
const dailyDate = ref(new Date().toISOString().split('T')[0]);
const weeklyStart = ref(new Date().toISOString().split('T')[0]);
const weeklyEnd = ref(new Date().toISOString().split('T')[0]);
const monthlyMonth = ref(new Date().getMonth() + 1);
const monthlyYear = ref(new Date().getFullYear());

const reportData = ref(props.dailyReport);

const tabs = [
    { id: 'daily', name: 'Daily' },
    { id: 'weekly', name: 'Weekly' },
    { id: 'monthly', name: 'Monthly' },
];

const today = computed(() => new Date().toISOString().split('T')[0]);

const years = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 5 }, (_, i) => currentYear - i);
});

const getMonthName = (month) => {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[month - 1];
};

watch(dailyDate, () => {
    if (activeTab.value === 'daily' && props.currentStore) {
        router.reload({ data: { date: dailyDate.value } });
    }
});
</script>
