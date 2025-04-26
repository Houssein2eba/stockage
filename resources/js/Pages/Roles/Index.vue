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
import Pagination from "@/Components/Pagination.vue";
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
const page = ref(props.roles?.meta?.current_page || 1);

// Watch for search changes only
watch([search], debounce(() => {
    router.get(route('roles.index'), {
        search: search.value,
        sort: sort.value.field,
        direction: sort.value.direction,
        page: 1 // Reset to page 1 when searching
    }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300));

// Table headers configuration
const tableHeaders = computed(() => [
    { label: 'Role Name', field: 'name', sortable: true },
    { label: 'Permissions', field: null, sortable: false },
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
    
    // Keep the current page when sorting
    router.get(route('roles.index'), {
        search: search.value,
        sort: sort.value.field,
        direction: sort.value.direction,
        page: page.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

// Handle pagination link clicks
const handlePageChange = (url) => {
    if (!url) return;
    
    // Extract page number from URL
    const urlObj = new URL(url);
    const pageParam = urlObj.searchParams.get('page');
    
    if (pageParam) {
        page.value = parseInt(pageParam);
        
        router.get(route('roles.index'), {
            search: search.value,
            sort: sort.value.field,
            direction: sort.value.direction,
            page: page.value
        }, {
            preserveState: true,
            preserveScroll: true
        });
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
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
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
                    <H1>Roles & Permissions</H1>
                    <P>Manage roles and their permissions mr 
                    </P>
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
                    <Table >
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
                        <template #body>
                            <TableRow v-for="role in props.roles.data" :key="role.id">
                                
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
                                
                            </TableRow>
                            <TableRow v-if="props.roles_count === 0">
                                <TableDataCell colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                        <P>No roles found</P>
                                        <P>Add a new role using the form.</P>
                                    </div>
                                </TableDataCell>
                            </TableRow>
                        </template>
                    </Table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700" v-if="props.roles.meta && props.roles.meta.total > 0">
                            Showing <span class="font-medium">{{ props.roles.meta.from }}</span> to 
                            <span class="font-medium">{{ props.roles.meta.to }}</span> of 
                            <span class="font-medium">{{ props.roles.meta.total }}</span> results
                        </div>
                        <Pagination v-if="props.roles.meta && props.roles.meta.links" :links="props.roles.meta.links" @change="handlePageChange" />
                    </div>
                </div>
            </div>

            <!-- Delete Role Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                    <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div>
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Delete Role
                                </h3>
                                <div class="mt-2">
                                    <P>
                                        Are you sure you want to delete this role? All users with this role will lose their permissions. This action cannot be undone.
                                    </P>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button
                                type="button"
                                @click="deleteRole"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm"
                            >
                                Delete
                            </button>
                            <button
                                type="button"
                                @click="showDeleteModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
