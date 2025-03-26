<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { ref } from 'vue'
import {router} from '@inertiajs/vue3';
import {useToast} from "vue-toastification";
import {Link} from '@inertiajs/vue3';
const props = defineProps({
    roles: {
        type: Array,
        required: true,
        default: () => []
    }
})
const numberUsers = (role) => {
    console.log(role)
    return role.users.length
}
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
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="role in roles" :key="role.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ role.id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ role.name }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            <span 
                                v-for="permission in role.permissions" 
                                :key="permission.id"
                                class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"
                            >
                                {{ permission.name }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ role.users_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button
                            @click="router.visit(`/roles/${role.id}`)"
                            
                            class="text-blue-600 hover:text-blue-900 mr-3"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                        <button
                            @click="confirmDelete(role.id)"
                            :disabled="role.is_default"
                            class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
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