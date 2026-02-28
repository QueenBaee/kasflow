<template>
    <div class="relative" ref="container">
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">{{ label }}</label>
        <div class="relative">
            <input
                v-model="searchQuery"
                @focus="isOpen = true"
                @input="handleSearch"
                type="text"
                :placeholder="placeholder"
                :required="required"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-500': error }"
            />
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
        
        <div
            v-if="isOpen && filteredOptions.length > 0"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto"
        >
            <div
                v-for="option in filteredOptions"
                :key="option.value"
                @click="selectOption(option)"
                class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition-colors"
                :class="{ 'bg-blue-100': modelValue === option.value }"
            >
                <div class="font-medium text-gray-900">{{ option.label }}</div>
                <div v-if="option.subtitle" class="text-sm text-gray-500">{{ option.subtitle }}</div>
            </div>
        </div>
        
        <div
            v-if="isOpen && filteredOptions.length === 0 && searchQuery"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-4 text-center text-gray-500"
        >
            No results found
        </div>
        
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number], default: null },
    options: { type: Array, required: true },
    label: { type: String, default: '' },
    placeholder: { type: String, default: 'Search...' },
    required: { type: Boolean, default: false },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const searchQuery = ref('');
const isOpen = ref(false);
const container = ref(null);

const filteredOptions = computed(() => {
    if (!searchQuery.value) return props.options;
    
    const query = searchQuery.value.toLowerCase();
    return props.options.filter(option => 
        option.label.toLowerCase().includes(query) ||
        (option.subtitle && option.subtitle.toLowerCase().includes(query))
    );
});

const selectOption = (option) => {
    emit('update:modelValue', option.value);
    searchQuery.value = option.label;
    isOpen.value = false;
};

const handleSearch = () => {
    isOpen.value = true;
    if (!searchQuery.value) {
        emit('update:modelValue', null);
    }
};

const handleClickOutside = (event) => {
    if (container.value && !container.value.contains(event.target)) {
        isOpen.value = false;
    }
};

watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        const selected = props.options.find(opt => opt.value === newValue);
        if (selected) {
            searchQuery.value = selected.label;
        }
    } else {
        searchQuery.value = '';
    }
}, { immediate: true });

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
