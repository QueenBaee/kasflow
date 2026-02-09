<template>
    <div>
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
        </label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-lg font-semibold">
                Rp
            </span>
            <input
                :id="id"
                ref="inputRef"
                type="text"
                inputmode="numeric"
                :value="displayValue"
                @input="handleInput"
                @focus="$event.target.select()"
                :placeholder="placeholder"
                :disabled="disabled"
                class="w-full pl-14 pr-4 py-4 text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                :class="{ 'border-red-500': error }"
            />
        </div>
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { formatCurrency, parseCurrency } from '../Utils/currency';

const props = defineProps({
    modelValue: [Number, String],
    label: String,
    placeholder: { type: String, default: '0' },
    error: String,
    disabled: Boolean,
    id: String,
});

const emit = defineEmits(['update:modelValue']);

const inputRef = ref(null);
const localValue = ref(props.modelValue || 0);

const displayValue = computed(() => {
    if (!localValue.value) return '';
    return new Intl.NumberFormat('id-ID').format(localValue.value);
});

const handleInput = (event) => {
    const value = event.target.value.replace(/\D/g, '');
    localValue.value = value ? parseInt(value) : 0;
    emit('update:modelValue', localValue.value);
};

watch(() => props.modelValue, (newValue) => {
    localValue.value = newValue || 0;
});

defineExpose({ focus: () => inputRef.value?.focus() });
</script>
