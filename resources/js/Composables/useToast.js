import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const toast = ref(null);

export function useToast() {
    const page = usePage();
    
    watch(() => page.props.flash, (flash) => {
        if (flash.success) {
            showToast(flash.success, 'success');
        }
        if (flash.error) {
            showToast(flash.error, 'error');
        }
    }, { deep: true });
    
    const showToast = (message, type = 'success') => {
        toast.value = { message, type };
        setTimeout(() => {
            toast.value = null;
        }, 3000);
    };
    
    return {
        toast,
        showToast,
    };
}
