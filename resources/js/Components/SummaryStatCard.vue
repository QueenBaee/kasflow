<template>
    <div class="bg-white rounded-lg shadow p-6 border-l-4" :class="borderColor">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">{{ label }}</p>
                <p class="text-3xl font-bold" :class="valueColor">{{ formattedValue }}</p>
                <p v-if="subtitle" class="text-xs text-gray-500 mt-1">{{ subtitle }}</p>
            </div>
            <div v-if="icon" class="flex-shrink-0 ml-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center" :class="iconBg">
                    <component :is="icon" class="w-6 h-6" :class="iconColor" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatCurrency } from '../Utils/currency';

const props = defineProps({
    label: { type: String, required: true },
    value: { type: Number, required: true },
    type: { type: String, default: 'default' },
    subtitle: String,
    icon: Object,
    format: { type: String, default: 'currency' },
});

const formattedValue = computed(() => {
    if (props.format === 'currency') {
        return formatCurrency(props.value);
    }
    return props.value.toLocaleString('id-ID');
});

const borderColor = computed(() => {
    const colors = {
        income: 'border-green-500',
        expense: 'border-red-500',
        profit: 'border-blue-500',
        default: 'border-gray-300',
    };
    return colors[props.type] || colors.default;
});

const valueColor = computed(() => {
    const colors = {
        income: 'text-green-600',
        expense: 'text-red-600',
        profit: props.value >= 0 ? 'text-blue-600' : 'text-red-600',
        default: 'text-gray-900',
    };
    return colors[props.type] || colors.default;
});

const iconBg = computed(() => {
    const colors = {
        income: 'bg-green-100',
        expense: 'bg-red-100',
        profit: 'bg-blue-100',
        default: 'bg-gray-100',
    };
    return colors[props.type] || colors.default;
});

const iconColor = computed(() => {
    const colors = {
        income: 'text-green-600',
        expense: 'text-red-600',
        profit: 'text-blue-600',
        default: 'text-gray-600',
    };
    return colors[props.type] || colors.default;
});
</script>
