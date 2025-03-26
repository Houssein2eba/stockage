import { usePage } from "@inertiajs/vue3";

export function useAdmin() {
    const isAdmin = usePage().props.auth.guard === 'admin';

    return { isAdmin };
}