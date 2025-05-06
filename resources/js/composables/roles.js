import { usePage } from "@inertiajs/vue3";

export function useRole() {
    const hasRole = (name) => {
        const roles = usePage().props.auth.roles;
        if (!roles) return false;
        return roles.name === name;
    };

    return { hasRole };
}
