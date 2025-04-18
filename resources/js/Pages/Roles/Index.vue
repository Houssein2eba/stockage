

<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { ref } from 'vue'
import {router} from '@inertiajs/vue3';
import {useToast} from "vue-toastification";
import { Link } from '@inertiajs/vue3';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";



const date = ref();
const props = defineProps({
    roles: {
        type: Array,
        required: true,
        default: () => []
    }
})

const toast=useToast();
const showDeleteModal = ref(false)
const roleToDelete = ref(null)

const confirmDelete = (role) => {
    roleToDelete.value = role
    showDeleteModal.value = true
}

const deleteRole = () => {

   router.delete(`/roles/${roleToDelete.value}`, {
        onSuccess: () => {
            toast.success('Role deleted successfully')
        },
        onError: () => {
            toast.error('Failed to delete role')
        },
    })
    showDeleteModal.value = false
}

defineOptions({
    layout: AuthLayout
})
</script>

<template>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Roles & Permissions</h1>

        <Link
            :href="route('roles.create')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>

          Add Role</Link>

    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">



        <Table>
            <template #header>
                <TableRow>
                    <TableHeaderCell>Role Name</TableHeaderCell>
                    <TableHeaderCell>Permissions</TableHeaderCell>
                     <TableHeaderCell>Employees</TableHeaderCell>
                    <TableHeaderCell :colspan=2>Actions</TableHeaderCell>
                </TableRow>
            </template>
            <template #default>
                <TableRow v-for="role in props.roles.data" :key="role.id">
                    <TableDataCell>{{ role.name }}</TableDataCell>
                    <TableDataCell >
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
                    <TableDataCell >{{ role.users_count }}</TableDataCell>
                      
                        <TableDataCell>
                            <button @click="confirmDelete(role)" class="text-red-600 hover:text-red-800">
                                  Delete
                            </button>
                        </TableDataCell>
                        <TableDataCell >
                        <Link :href="route('roles.edit', role.id)" class="text-blue-600 hover:text-blue-800">
                            Edit
                        </Link>
                        </TableDataCell>
                </TableRow>
            </template>
        </Table>
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
                Are you sure you want to delete this role ? This action cannot be undone.
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
</template>
