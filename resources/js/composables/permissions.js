import { usePage } from "@inertiajs/vue3";

export function usePermission() {
    const hasRole = (name) => usePage().props.auth?.roles?.name === name;
const hasPermission = (name) => {
  const permissions = usePage().props.auth?.permissions ?? [];
  return permissions.map(p => p.name).includes(name);
};


    return { hasRole, hasPermission };
}
