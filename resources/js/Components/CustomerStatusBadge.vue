<template>
    <span 
        :class="badgeClass"
        class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap"
    >
        {{ statusText }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
        validator: (value) => ['AKTIF', 'ISOLIR', 'NONAKTIF'].includes(value)
    }
});

const badgeClass = computed(() => {
    switch (props.status) {
        case 'AKTIF':
            return 'bg-green-100 text-green-800';
        case 'ISOLIR':
            return 'bg-red-100 text-red-800';
        case 'NONAKTIF':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
});

const statusText = computed(() => {
    switch (props.status) {
        case 'AKTIF':
            return 'Aktif';
        case 'ISOLIR':
            return 'Isolir';
        case 'NONAKTIF':
            return 'Nonaktif';
        default:
            return props.status;
    }
});
</script>
