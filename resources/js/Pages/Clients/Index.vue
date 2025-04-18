<script setup >
import { defineProps } from "vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import { ref } from 'vue';

const form = useForm({
    name: "",
    number: "",
});

const props = defineProps({
    clients: Array
});

const toast = useToast();

// Delete client logic
const showDeleteModal = ref(false);
const clientToDelete = ref(null);

const confirmDelete = (clientId) => {
    clientToDelete.value = clientId;
    showDeleteModal.value = true;
};

const deleteClient = () => {
    form.delete(route('clients.destroy', clientToDelete.value), {
        onSuccess: () => {
            toast.success('Client deleted successfully');
            showDeleteModal.value = false;
            clientToDelete.value = null;
        },
        onError: () => {
            toast.error('Failed to delete client');
            showDeleteModal.value = false;
            clientToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    clientToDelete.value = null;
};

// Format phone number for display
const formatPhoneNumber = (number) => {
    if (!number) return '';
    // Simple formatting - adjust based on your needs
    return number.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
};
</script>

<template>
    <AuthLayout>
        <Head title="Client Management" ></Head>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header with stats -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Clients Management</h1>
                    <p class="text-gray-600 mt-1">Manage your clients</p>
                </div>
                <div class="flex items-center gap-3 bg-blue-50/80 px-4 py-2 rounded-lg border border-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    <span class="text-sm font-medium text-blue-800">
                        Total Clients: <span class="font-semibold">{{ clients.length }}</span>
                    </span>
                </div>
            </div>

            <!-- Two-column layout -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Main content area (table) -->
                <div class="flex-1 overflow-hidden">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs h-fit flex flex-col">
                        <div class="overflow-x-auto flex-1">
                            <div class="min-w-[1024px]">
                                <Table class="w-full">
                                    <thead class="bg-gray-50">
                                        <TableRow class="border-b border-gray-200">
                                            <TableHeaderCell class="w-[200px] py-3 pl-6">Client Name</TableHeaderCell>
                                            <TableHeaderCell class="w-[300px]">Contact Info</TableHeaderCell>
                                            <TableHeaderCell class="w-[150px]">Phone</TableHeaderCell>
                                            <TableHeaderCell class="w-[200px]">Projects</TableHeaderCell>
                                            <TableHeaderCell class="w-[150px] text-right pr-6">Actions</TableHeaderCell>
                                        </TableRow>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <TableRow v-for="client in clients" :key="client.id" class="hover:bg-gray-50/50 transition-colors">
                                            <TableDataCell class="py-4 pl-6">
                                                <div class="font-medium text-gray-900">{{ client.name }}</div>
                                                <div class="text-sm text-gray-500 mt-1">ID: {{ client.id }}</div>
                                            </TableDataCell>
                                            <TableDataCell class="text-gray-600 text-sm">
                                                <div>{{ client.email || "No email" }}</div>
                                                <div class="text-xs text-gray-400 mt-1">Since {{ new Date(client.created_at).toLocaleDateString() }}</div>
                                            </TableDataCell>
                                            <TableDataCell>
                                                <div class="font-mono text-sm">
                                                    {{ formatPhoneNumber(client.number) || "N/A" }}
                                                </div>
                                            </TableDataCell>
                                            <TableDataCell class="text-sm">
                                                <div class="flex flex-wrap gap-1">
                                                    <span v-if="client.projects_count > 0" class="px-2.5 py-1 bg-green-100/80 text-green-800 rounded-full text-xs">
                                                        {{ client.projects_count }} active projects
                                                    </span>
                                                    <span v-else class="px-2.5 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                                                        No projects
                                                    </span>
                                                </div>
                                            </TableDataCell>
                                            <TableDataCell class="text-right pr-6">
                                                <div class="flex items-center justify-end gap-3">
                                                    <Link
                                                        :href="route('clients.edit', client.id)"
                                                        class="text-blue-600 hover:text-blue-900 transition-colors flex items-center gap-1"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit
                                                    </Link>
                                                    <button
                                                        @click.prevent="confirmDelete(client.id)"
                                                        class="text-red-600 hover:text-red-900 transition-colors flex items-center gap-1"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </div>
                                            </TableDataCell>
                                        </TableRow>
                                        <TableRow v-if="clients.length === 0">
                                            <TableDataCell colspan="5" class="text-center py-12 text-gray-500">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    <p class="mt-3 text-gray-600 font-medium">No clients found</p>
                                                    <p class="text-sm text-gray-400 mt-1">Add your first client using the form</p>
                                                </div>
                                            </TableDataCell>
                                        </TableRow>
                                    </tbody>
                                </Table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar form -->
                <div class="lg:w-96">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs sticky top-6">
                        <div class="p-5 border-b border-gray-200 bg-gray-50/50">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add New Client
                            </h2>
                        </div>
                        <form @submit.prevent="form.post(route('clients.store'), {
                            onSuccess: () => {
                                toast.success('Client created successfully');
                                form.reset();
                            },
                            onError: () => {
                                toast.error('Failed to create client');
                            },
                        })" class="p-5 space-y-5">
                            <div>
                                <InputLabel for="name" value="Client Name" class="mb-1.5" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="w-full"
                                    v-model="form.name"
                                    placeholder="Enter client name"
                                    :class="{ 'border-red-500': form.errors.name }"
                                />
                                <InputError class="mt-1.5" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="number" value="Phone Number" class="mb-1.5" />
                                <TextInput
                                    id="number"
                                    type="tel"
                                    class="w-full"
                                    v-model="form.number"
                                    placeholder="Enter phone number"
                                    :class="{ 'border-red-500': form.errors.number }"
                                />
                                <InputError class="mt-1.5" :message="form.errors.number" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email (Optional)" class="mb-1.5" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="w-full"
                                    v-model="form.email"
                                    placeholder="Enter email address"
                                    :class="{ 'border-red-500': form.errors.email }"
                                />
                                <InputError class="mt-1.5" :message="form.errors.email" />
                            </div>

                            <div class="pt-2">
                                <PrimaryButton
                                    class="w-full justify-center py-2.5"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span v-if="!form.processing" class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Client
                                    </span>
                                    <span v-else class="flex items-center gap-2">
                                        <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <Transition name="fade">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Client</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Are you sure you want to delete this client? This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton
                                @click="deleteClient"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing">Delete</span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Deleting...
                                </span>
                            </PrimaryButton>
                            <button
                                @click="cancelDelete"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthLayout>
</template>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 150ms ease-out;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
