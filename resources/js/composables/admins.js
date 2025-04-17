import { usePage } from "@inertiajs/vue3";

export function useAdmin() {
    const isAdmin = usePage().props.auth.roles.name==="admin";

    return { isAdmin };
}
