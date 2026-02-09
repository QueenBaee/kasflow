import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

export function useAuth() {
    const page = usePage();
    
    const user = computed(() => page.props.auth.user);
    const isAuthenticated = computed(() => !!user.value);
    
    const logout = () => {
        router.post('/logout');
    };
    
    return {
        user,
        isAuthenticated,
        logout,
    };
}
