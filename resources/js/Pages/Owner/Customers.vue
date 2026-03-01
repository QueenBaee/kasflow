<template>
    <OwnerLayout>
        <div class="space-y-4 md:space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">Pelanggan</h1>
                <BaseButton @click="showAddModal = true" variant="primary" size="sm" class="md:text-base">
                    + Tambah
                </BaseButton>
            </div>

            <!-- Search Box -->
            <div class="bg-white rounded-lg shadow p-4">
                <input 
                    v-model="searchQuery" 
                    type="text" 
                    placeholder="Cari nama, telepon, atau alamat..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>

            <!-- Mobile View - Card Style -->
            <div class="lg:hidden space-y-3">
                <div v-for="customer in filteredCustomers" :key="customer.id" class="rounded-lg shadow p-4" :class="customer.status === 'ISOLIR' ? 'bg-red-50' : 'bg-white'">
                    <div class="space-y-3">
                        <!-- Header -->
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 text-base">{{ customer.name }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ customer.phone || '-' }}</div>
                            </div>
                            <div class="flex gap-2">
                                <PaymentStatusBadge :status="customer.payment_status" />
                                <CustomerStatusBadge :status="customer.status || 'AKTIF'" />
                            </div>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-2 gap-3 text-sm pt-3 border-t border-gray-100">
                            <div>
                                <div class="text-gray-500 text-xs">Paket</div>
                                <div class="font-medium text-gray-900 mt-0.5">{{ customer.speed_package }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 text-xs">Biaya/Bulan</div>
                                <div class="font-medium text-gray-900 mt-0.5">{{ formatCurrency(customer.monthly_fee) }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 text-xs">Jatuh Tempo</div>
                                <div class="font-medium text-gray-900 mt-0.5">Tgl {{ customer.due_date || '-' }}</div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div v-if="customer.address" class="text-sm text-gray-600 pt-2 border-t border-gray-100">
                            <div class="text-gray-500 text-xs mb-1">Alamat</div>
                            {{ customer.address }}
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-3 border-t border-gray-100">
                            <button @click="editCustomer(customer)" class="flex-1 px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 font-medium">
                                Ubah
                            </button>
                            <button @click="deleteCustomer(customer)" class="flex-1 px-3 py-2 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 font-medium">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="filteredCustomers.length === 0" class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                    {{ searchQuery ? 'Tidak ada pelanggan yang cocok' : 'Belum ada pelanggan' }}
                </div>
            </div>

            <!-- Desktop View - Fixed Width Table -->
            <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-[12%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="w-[10%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                            <th class="w-[16%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                            <th class="w-[10%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                            <th class="w-[10%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
                            <th class="w-[8%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">J. Tempo</th>
                            <th class="w-[10%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                            <th class="w-[8%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="w-[16%] px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="customer in filteredCustomers" :key="customer.id" :class="customer.status === 'ISOLIR' ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50'">
                            <td class="px-3 py-3 text-sm font-medium text-gray-900 truncate" :title="customer.name">{{ customer.name }}</td>
                            <td class="px-3 py-3 text-sm text-gray-500 truncate" :title="customer.phone">{{ customer.phone || '-' }}</td>
                            <td class="px-3 py-3 text-sm text-gray-500 truncate" :title="customer.address">{{ customer.address || '-' }}</td>
                            <td class="px-3 py-3 text-sm text-gray-500 truncate" :title="customer.speed_package">{{ customer.speed_package }}</td>
                            <td class="px-3 py-3 text-sm text-gray-500 truncate" :title="formatCurrency(customer.monthly_fee)">{{ formatCurrency(customer.monthly_fee) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-500">{{ customer.due_date ? `Tgl ${customer.due_date}` : '-' }}</td>
                            <td class="px-3 py-3">
                                <PaymentStatusBadge :status="customer.payment_status" />
                            </td>
                            <td class="px-3 py-3">
                                <CustomerStatusBadge :status="customer.status || 'AKTIF'" />
                            </td>
                            <td class="px-3 py-3 text-right text-sm font-medium">
                                <button @click="editCustomer(customer)" class="text-blue-600 hover:text-blue-900 mr-2">Ubah</button>
                                <button @click="deleteCustomer(customer)" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="filteredCustomers.length === 0">
                            <td colspan="9" class="px-3 py-8 text-center text-gray-500">{{ searchQuery ? 'Tidak ada pelanggan yang cocok' : 'Belum ada pelanggan' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showAddModal || editingCustomer" class="fixed inset-0 bg-gray-900 bg-opacity-20 flex items-center justify-center z-50 p-4" @click.self="closeModal">
            <div class="bg-white rounded-lg shadow-xl p-4 md:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg md:text-xl font-bold mb-4">{{ editingCustomer ? 'Ubah Pelanggan' : 'Tambah Pelanggan' }}</h2>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input v-model="form.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon (Opsional)</label>
                        <input v-model="form.phone" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat (Opsional)</label>
                        <textarea v-model="form.address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paket Kecepatan</label>
                        <input v-model="form.speed_package" type="text" required placeholder="contoh: 10 Mbps" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Bulanan (Rp)</label>
                        <input v-model="form.monthly_fee" type="number" required min="0" placeholder="contoh: 300000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung (Opsional)</label>
                        <input v-model="form.join_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo (Tanggal dalam Bulan, Opsional)</label>
                        <input v-model="form.due_date" type="number" min="1" max="31" placeholder="contoh: 5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select v-model="form.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="AKTIF">Aktif</option>
                            <option value="ISOLIR">Isolir</option>
                            <option value="NONAKTIF">Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <BaseButton type="submit" variant="primary" :loading="form.processing" class="flex-1 w-full">
                            {{ editingCustomer ? 'Perbarui' : 'Tambah' }}
                        </BaseButton>
                        <BaseButton type="button" @click="closeModal" variant="secondary" class="flex-1 w-full">
                            Batal
                        </BaseButton>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import OwnerLayout from '../../Layouts/OwnerLayout.vue';
import BaseButton from '../../Components/BaseButton.vue';
import PaymentStatusBadge from '../../Components/PaymentStatusBadge.vue';
import CustomerStatusBadge from '../../Components/CustomerStatusBadge.vue';

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const props = defineProps({
    stores: { type: Array, required: true },
    currentStore: { type: Object, default: null },
    customers: { type: Array, default: () => [] },
});

const showAddModal = ref(false);
const editingCustomer = ref(null);
const searchQuery = ref('');

const filteredCustomers = computed(() => {
    if (!searchQuery.value) return props.customers;
    
    const query = searchQuery.value.toLowerCase();
    return props.customers.filter(customer => {
        return (
            customer.name?.toLowerCase().includes(query) ||
            customer.phone?.toLowerCase().includes(query) ||
            customer.address?.toLowerCase().includes(query)
        );
    });
});

const form = useForm({
    name: '',
    phone: '',
    address: '',
    speed_package: '',
    monthly_fee: 0,
    join_date: '',
    due_date: null,
    status: 'AKTIF',
});

const editCustomer = (customer) => {
    editingCustomer.value = customer;
    form.name = customer.name;
    form.phone = customer.phone;
    form.address = customer.address;
    form.speed_package = customer.speed_package;
    form.monthly_fee = customer.monthly_fee;
    form.join_date = customer.join_date;
    form.due_date = customer.due_date;
    form.status = customer.status || 'AKTIF';
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
