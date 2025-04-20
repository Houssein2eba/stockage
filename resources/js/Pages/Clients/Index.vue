<script setup>
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
import VueMultiselect from 'vue-multiselect';
import { ref } from 'vue';

const props = defineProps({
    clients:Array
});
const form = useForm({
    id: null,
    name: "",
    number: "",
});
console.log(props.clients);

const toast = useToast();

// delete Client logic
const showDeleteModal = ref(false);
const clientToDelete = ref(null);

const confirmDelete = (clientId) => {
  clientToDelete.value = clientId;
  showDeleteModal.value = true;
};

const deleteClient = () => {
  form.delete(route('clients.destroy', clientToDelete.value), {
    onSuccess: () => {
      toast.success('client deleted successfully');
      showDeleteModal.value = false;
      clientToDelete.value = null;
    },
    onError: () => {
      toast.error('Failed to delete Client');
      showDeleteModal.value = false;
      clientToDelete.value = null;
    },
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  clientToDelete.value = null;
};
</script>

<template>
  <AuthLayout>
    <Head title="Clients Management" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header with stats -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Client Management</h1>
          <p class="text-gray-600 mt-1">Manage your clients</p>
        </div>
        <div class="flex items-center gap-3 bg-blue-50/80 px-4 py-2 rounded-lg border border-blue-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
          </svg>
          <span class="text-sm font-medium text-blue-800">
            Total Clients: <span class="font-semibold">{{ props.clients.data.length }}</span>
          </span>
        </div>
      </div>

      <!-- Two-column layout -->
      <div class="flex flex-col lg:flex-row gap-6">
        <!-- Main content area (table) -->
        <div class="flex-1 overflow-hidden">
          <div class="bg-white rounded-lg border border-gray-200 shadow-xs h-fit flex flex-col">
            <div class="overflow-x-auto flex-1">
              <Table class="w-full">
                <thead class="bg-gray-50">
                  <TableRow class="border-b border-gray-200">
                    <TableHeaderCell class="whitespace-nowrap">Name</TableHeaderCell>
                    <TableHeaderCell class="whitespace-nowrap">Number</TableHeaderCell>
                    <TableHeaderCell :colspan="2" class="whitespace-nowrap text-right pr-6">Actions</TableHeaderCell>
                  </TableRow>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <TableRow v-for="client in props.clients.data" :key="client.id" class="hover:bg-gray-50/50 transition-colors">
                    <TableDataCell class="py-4 px-4 md:px-6 whitespace-nowrap">
                      <div class="font-medium text-gray-900">{{ client.name }}</div>
                    </TableDataCell>
                    <TableDataCell class="text-gray-600 text-sm px-4 md:px-6 whitespace-nowrap">
                      {{ client.number }}
                    </TableDataCell>
                    <TableDataCell class="px-4 md:px-6 whitespace-nowrap">
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
                  <TableRow v-if="props.clients.data.length === 0">
                    <TableDataCell colspan="3" class="text-center py-12 text-gray-500">
                      <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
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
                previewImage = null;
              },
              onError: () => {
                toast.error('Failed to create Client');
              },
            })" class="p-5 space-y-5">
              <div>
                <InputLabel for="name" value="Client Name" class="mb-1.5" />
                <TextInput
                  id="name"
                  type="text"
                  class="w-full"
                  v-model="form.name"
                  placeholder="Enter Client name"
                  :class="{ 'border-red-500': form.errors.name }"
                />
                <InputError class="mt-1.5" :message="form.errors.name" />
              </div>

              <div>
                <InputLabel for="number" value="Client Number" class="mb-1.5" />
                <TextInput
                  id="number"
                  type="text"
                  class="w-full"
                  v-model="form.number"
                  placeholder="Enter Client number"
                  :class="{ 'border-2 border-red-500': form.errors.number }"
                />
                <InputError class="mt-1.5" :message="form.errors.number" />
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
                    <p class="text-sm text-gray-500">Are you sure you want to delete this Client? This action cannot be undone.</p>
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
                @click="showDeleteModal = false"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-700 text-base font-medium text-white  focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto sm:text-sm"
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

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 150ms ease-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
