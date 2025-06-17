import { usePage } from "@inertiajs/vue3";



export function usePermission() {
    const hasRole = (name) => usePage().props.auth?.roles?.name === name;
    
    const hasPermission = (name) => {
        const permissions = usePage().props.auth?.permissions ?? [];
        return permissions.includes(name);
    };

    return { hasRole, hasPermission };
}