import { usePage } from "@inertiajs/vue3";

export function usePermission() {
    const hasRole = (name) => usePage().props.auth.roles.includes(name);
    const hasPermission = (name) =>
        usePage().props.auth.permissions.includes(name);

    return { hasRole, hasPermission };
}
