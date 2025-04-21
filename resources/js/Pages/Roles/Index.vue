<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { ref, watch, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import { debounce } from 'lodash';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import { useAdmin } from '@/composables/admins';
const props = defineProps({
    roles: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});
const { isAdmin } = useAdmin();
const toast = useToast();
const search = ref(props.filters?.search || '');
const sort = ref({ field: props.filters?.sort || 'created_at', direction: props.filters?.direction || 'desc' });

// Watch for search and sort changes
watch([search, sort], debounce(() => {
    router.get(route('roles.index'), {
        search: search.value,
        sort: sort.value.field,
        direction: sort.value.direction
    }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300), { deep: true });

// Table headers configuration
const tableHeaders = computed(() => [
    { label: 'Role Name', field: 'name', sortable: true },
    { label: 'Permissions', field: 'permissions_count', sortable: true },
    { label: 'Employees', field: 'users_count', sortable: true },
    { label: 'Actions', field: null, sortable: false, colspan: 2 }
]);

const handleSort = (field) => {
    if (!field || !tableHeaders.value.find(header => header.field === field)?.sortable) return;

    if (sort.value.field === field) {
        sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value.field = field;
        sort.value.direction = 'asc';
    }
};

const getSortIcon = (field) => {
    if (!field || sort.value.field !== field) return 'none';
    return sort.value.direction === 'asc' ? 'asc' : 'desc';
};

const showDeleteModal = ref(false);
const roleToDelete = ref(null);

const confirmDelete = (role) => {
    roleToDelete.value = role;
    showDeleteModal.value = true;
};

const deleteRole = () => {
    router.delete(`/roles/${roleToDelete.value}`, {
        onSuccess: () => {
            toast.success('Role deleted successfully');
            showDeleteModal.value = false;
        },
        onError: () => {
            toast.error('Failed to delete role');
        },
    });
};
</script>

<template>
    <AuthLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Roles & Permissions</h1>
                    <p class="text-gray-600 mt-1">Manage roles and their permissions mr 
                        {{isAdmin}}</p>
                </div>
                <Link
                    :href="route('roles.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Role
                </Link>
            </div>

            <!-- Search Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs mb-6">
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    v-model="search"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Search roles..."
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <Table class="min-w-full divide-y divide-gray-200">
                        <template #header>
                            <TableRow>
                                <TableHeaderCell
                                    v-for="header in tableHeaders"
                                    :key="header.field || header.label"
                                    :class="[
                                        header.sortable ? 'cursor-pointer hover:bg-gray-100' : '',
                                        'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'
                                    ]"
                                    :colspan="header.colspan"
                                    @click="handleSort(header.field)"
                                >
                                    <div class="flex items-center space-x-1">
                                        <span>{{ header.label }}</span>
                                        <span v-if="header.sortable" class="flex flex-col">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3"
                                                :class="{'text-blue-600': getSortIcon(header.field) === 'asc'}"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                            </svg>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3"
                                                :class="{'text-blue-600': getSortIcon(header.field) === 'desc'}"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </TableHeaderCell>
                            </TableRow>
                        </template>
                        <template #default>
                            <TableRow v-for="role in props.roles.data" :key="role.id">
                                <template v-if="role.name !== 'admin'">
                                    <TableDataCell class="px-6 py-4">{{ role.name }}</TableDataCell>
                                    <TableDataCell class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="permission in role.permissions"
                                                :key="permission.id"
                                                class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"
                                            >
                                                {{ permission.name }}
                                            </span>
                                        </div>
                                    </TableDataCell>
                                    <TableDataCell class="px-6 py-4">{{ role.users_count }}</TableDataCell>
                                    <TableDataCell class="px-6 py-4">
                                        <button @click="confirmDelete(role.id)" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Delete
                                        </button>
                                    </TableDataCell>
                                    <TableDataCell class="px-6 py-4">
                                        <Link :href="route('roles.edit', role.id)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Edit
                                        </Link>
                                    </TableDataCell>
                                </template>
                            </TableRow>
                            <TableRow v-if="props.roles.data.length === 0">
                                <TableDataCell colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="mt-2">No roles found</p>
                                        <p class="text-sm text-gray-400">Add your first role using the form above</p>
                                    </div>
                                </TableDataCell>
                            </TableRow>
                        </template>
                    </Table>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Confirm Deletion</h3>
                    <button @click="showDeleteModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="mb-6 text-gray-600">
                    Are you sure you want to delete this role? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteRole"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
