<template>
    <OwnerLayout :stores="stores" :current-store="currentStore">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Catat Pengeluaran</h1>
            </div>

            <div class="max-w-2xl bg-white rounded-lg shadow p-6">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <CurrencyInput
                        v-model="form.amount"
                        label="Jumlah"
                        :error="form.errors.amount"
                        id="amount"
                    />

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                            Kategori
                        </label>
                        <select
                            id="category"
                            v-model="form.category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        >
                            <option value="">Pilih Kategori</option>
                            <option value="Supplies">Perlengkapan</option>
                            <option value="Rent">Sewa</option>
                            <option value="Utilities">Utilitas</option>
                            <option value="Salary">Gaji</option>
                            <option value="Other">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal
                        </label>
                        <input
                            id="date"
                            v-model="form.date"
                            type="date"
                            :max="today"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        />
                    </div>

                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan (Opsional)
                        </label>
                        <textarea
                            id="note"
                            v-model="form.note"
                            rows="3"
                            placeholder="Tambahkan catatan..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        ></textarea>
                    </div>

                    <div class="flex gap-3">
                        <BaseButton
                            type="submit"
                            variant="danger"
                            size="lg"
                            :loading="form.processing"
                            :disabled="!form.amount || form.amount <= 0"
                            class="flex-1"
                        >
                            Simpan Pengeluaran
                        </BaseButton>
                        <Link
                            href="/dashboard"
                            class="flex-1 text-center px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-semibold"
                        >
                            Batal
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import OwnerLayout from '../../Layouts/OwnerLayout.vue';
import CurrencyInput from '../../Components/CurrencyInput.vue';
import BaseButton from '../../Components/BaseButton.vue';

const props = defineProps({
    stores: { type: Array, default: () => [] },
    currentStore: Object,
});

const form = useForm({
    amount: 0,
    category: '',
    note: '',
    date: new Date().toISOString().split('T')[0],
});

const today = computed(() => new Date().toISOString().split('T')[0]);

const handleSubmit = () => {
    if (!props.currentStore) {
        alert('Please select a store first');
        return;
    }
    
    console.log('Submitting expense:', form.data());
    
    form.post(route('transactions.expense.store', props.currentStore.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Expense saved successfully');
            form.reset();
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
        },
    });
};
</script>
