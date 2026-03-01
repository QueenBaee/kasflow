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
        validator: (value) => ['LUNAS', 'BELUM_BAYAR', 'JATUH_TEMPO'].includes(value)
    }
});

const badgeClass = computed(() => {
    switch (props.status) {
        case 'LUNAS':
            return 'bg-green-100 text-green-800';
        case 'BELUM_BAYAR':
            return 'bg-red-100 text-red-800';
        case 'JATUH_TEMPO':
            return 'bg-yellow-100 text-yellow-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
});

const statusText = computed(() => {
    switch (props.status) {
        case 'LUNAS':
            return 'Lunas';
        case 'BELUM_BAYAR':
            return 'Belum Bayar';
        case 'JATUH_TEMPO':
            return 'Jatuh Tempo';
        default:
            return props.status;
    }
});
</script>
