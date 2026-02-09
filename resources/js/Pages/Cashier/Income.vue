<template>
    <CashierLayout :store-name="store.name">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Record Income</h2>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Amount Input -->
                    <CurrencyInput
                        v-model="form.amount"
                        label="Amount"
                        :error="form.errors.amount"
                        id="amount"
                        ref="amountInput"
                    />

                    <!-- Note (Optional, Collapsed) -->
                    <div>
                        <button
                            type="button"
                            @click="showNote = !showNote"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                        >
                            {{ showNote ? '- Hide Note' : '+ Add Note (Optional)' }}
                        </button>
                        <textarea
                            v-if="showNote"
                            v-model="form.note"
                            rows="3"
                            placeholder="Add a note..."
                            class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        ></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="space-y-3">
                        <BaseButton
                            type="submit"
                            variant="success"
                            size="xl"
                            :loading="form.processing"
                            :disabled="!form.amount || form.amount <= 0"
                            class="w-full"
                        >
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Income
                        </BaseButton>

                        <Link
                            href="/cashier"
                            class="block w-full text-center px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>

                <!-- Success Animation -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 scale-90"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-90"
                >
                    <div
                        v-if="showSuccess"
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                    >
                        <div class="bg-white rounded-2xl p-8 text-center">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">Success!</p>
                            <p class="text-gray-600 mt-2">Income recorded</p>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </CashierLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import CashierLayout from '../../Layouts/CashierLayout.vue';
import CurrencyInput from '../../Components/CurrencyInput.vue';
import BaseButton from '../../Components/BaseButton.vue';

const props = defineProps({
    store: { type: Object, required: true },
});

const form = useForm({
    amount: 0,
    note: '',
});

const showNote = ref(false);
const showSuccess = ref(false);
const amountInput = ref(null);

onMounted(() => {
    amountInput.value?.focus();
});

const handleSubmit = () => {
    form.post(route('transactions.income.store', props.store.id), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess.value = true;
            setTimeout(() => {
                showSuccess.value = false;
                form.reset();
                router.visit(route('cashier.home'));
            }, 1500);
        },
    });
};
</script>
