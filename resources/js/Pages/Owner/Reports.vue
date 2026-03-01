<template>
    <OwnerLayout :stores="stores" :current-store="currentStore">
        <div class="space-y-4 md:space-y-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Laporan Keuangan</h1>

            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Filter</label>
                            <select
                                v-model="filterType"
                                @change="onFilterTypeChange"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            >
                                <option value="date">Rentang Tanggal</option>
                                <option value="month">Bulanan</option>
                            </select>
                        </div>
                        <div v-if="filterType === 'date'" class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input
                                v-model="startDate"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                        <div v-if="filterType === 'date'" class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                            <input
                                v-model="endDate"
                                type="date"
                                :max="today"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                        <div v-if="filterType === 'month'" class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select
                                v-model="selectedMonth"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            >
                                <option v-for="month in months" :key="month.value" :value="month.value">
                                    {{ month.label }}
                                </option>
                            </select>
                        </div>
                        <div v-if="filterType === 'month'" class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select
                                v-model="selectedYear"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            >
                                <option v-for="year in years" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                        <button
                            @click="loadReport"
                            class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            Muat Laporan
                        </button>
                    </div>
                    
                    <div class="flex gap-2 flex-wrap">
                        <a
                            :href="exportPdfUrl"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                            Export PDF
                        </a>
                        <a
                            :href="exportCsvUrl"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <SummaryStatCard label="Pemasukan" :value="reportData.total_income" type="income" />
                        <SummaryStatCard label="Pengeluaran" :value="reportData.total_expense" type="expense" />
                        <SummaryStatCard label="Keuntungan" :value="reportData.profit" type="profit" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Detail Transaksi</h2>
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
                                {{ transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500" v-if="transaction.category">{{ transaction.category }}</div>
                        <div class="text-xs text-gray-500" v-if="transaction.note">{{ transaction.note }}</div>
                        <div class="text-right text-base font-semibold" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                            {{ formatCurrency(transaction.amount) }}
                        </div>
                    </div>
                    <div v-if="transactions.length === 0" class="p-8 text-center text-gray-500">
                        Tidak ada transaksi
                    </div>
                </div>

                <!-- Desktop View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in transactions" :key="transaction.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(transaction.transaction_date) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
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
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada transaksi</td>
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
const filterType = ref('date');
const selectedMonth = ref(new Date().getMonth() + 1);
const selectedYear = ref(new Date().getFullYear());

const today = computed(() => new Date().toISOString().split('T')[0]);

const months = [
    { value: 1, label: 'Januari' },
    { value: 2, label: 'Februari' },
    { value: 3, label: 'Maret' },
    { value: 4, label: 'April' },
    { value: 5, label: 'Mei' },
    { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' },
    { value: 8, label: 'Agustus' },
    { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' },
    { value: 11, label: 'November' },
    { value: 12, label: 'Desember' },
];

const years = computed(() => {
    const currentYear = new Date().getFullYear();
    const yearList = [];
    for (let i = currentYear; i >= currentYear - 5; i--) {
        yearList.push(i);
    }
    return yearList;
});

const onFilterTypeChange = () => {
    if (filterType.value === 'month') {
        const year = selectedYear.value;
        const month = String(selectedMonth.value).padStart(2, '0');
        const lastDay = new Date(year, selectedMonth.value, 0).getDate();
        startDate.value = `${year}-${month}-01`;
        endDate.value = `${year}-${month}-${lastDay}`;
    }
};

const exportPdfUrl = computed(() => {
    if (!props.currentStore) return '#';
    return route('reports.export.pdf', {
        store: props.currentStore.id,
        start_date: startDate.value,
        end_date: endDate.value,
    });
});

const exportCsvUrl = computed(() => {
    if (!props.currentStore) return '#';
    return route('reports.export.csv', {
        store: props.currentStore.id,
        start_date: startDate.value,
        end_date: endDate.value,
    });
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
};

const loadReport = () => {
    if (props.currentStore) {
        if (filterType.value === 'month') {
            const year = selectedYear.value;
            const month = String(selectedMonth.value).padStart(2, '0');
            const lastDay = new Date(year, selectedMonth.value, 0).getDate();
            startDate.value = `${year}-${month}-01`;
            endDate.value = `${year}-${month}-${lastDay}`;
        }
        router.reload({ 
            data: { 
                start_date: startDate.value,
                end_date: endDate.value
            } 
        });
    }
};
</script>
