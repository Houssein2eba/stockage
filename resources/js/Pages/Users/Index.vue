<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import Modal from "@/Components/Modal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification'
import { ref, computed, watch } from 'vue';
import { defineOptions, defineProps } from 'vue';
import { debounce } from 'lodash';
import Pagination from "@/Components/Pagination.vue";

defineOptions({
    layout:AuthLayout
});
const props = defineProps({
  users: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});
const showConfirmDeleteUserModal = ref(false);
const toast = useToast();
const deleteUser=(user)=> {
  router.delete(`/users/${user.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      // Show success message
      toast.success('User deleted successfully')
    },
    onError: (errors) => {
      // Show error message
      toast.error('Failed to delete user')
    },
    onFinish: () => {
      showConfirmDeleteUserModal = false;
    }
  }
)

}
const sort = ref({ field: props.filters?.sort || 'created_at', direction: props.filters?.direction || 'desc' });
const search = ref(props.filters?.search || '');
const page = ref(props.users?.meta?.current_page || 1);

// Watch for search changes only
watch([search], debounce(() => {
  router.get('/users', {
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
  { label: 'Name', field: 'name', sortable: true },
  { label: 'Role', field: 'roles.name', sortable: true },
  { label: 'Phone', field: 'number', sortable: true },
  { label: 'Email', field: 'email', sortable: true },
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
  router.get('/users', {
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
    
    router.get('/users', {
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
</script>

<template>
  <Head title="Users index" />

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Employees</h1>
        <p class="text-gray-600 mt-1">Manage your system users</p>
      </div>
      <Link
        :href="route('users.create')"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Add User
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
                placeholder="Search users..."
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="mt-6">
        <Table>
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
            <TableRow v-for="user in props.users.data" :key="user.id" class="border-b">
              <template v-if="user.roles.name !=='admin'">
                <TableDataCell>{{ user.name }}</TableDataCell>
                <TableDataCell>{{ user.roles.name }}</TableDataCell>
                <TableDataCell>{{ user.number }}</TableDataCell>
                <TableDataCell>{{ user.email }}</TableDataCell>
                <TableDataCell>
                  <button @click="showConfirmDeleteUserModal = true"  class="text-red-400 hover:text-red-600">
                    Delete
                  </button>
                  <Modal :show="showConfirmDeleteUserModal" >
                    <div class="p-6">
                      <h2 class="text-lg font-semibold text-slate-800">Are you sure to delete this user?</h2>
                      <div class="mt-6 flex space-x-4">
                        <DangerButton @click="deleteUser(user)">Delete</DangerButton>
                        <SecondaryButton @click="showConfirmDeleteUserModal = false">Cancel</SecondaryButton>
                      </div>
                    </div>
                  </Modal>
                </TableDataCell>
                <TableDataCell >
                  <Link
                    :href="route('users.edit', user.id)"
                    class="text-green-400 hover:text-green-600"
                  >
                    Edit
                  </Link>
                </TableDataCell>
              </template>
            </TableRow>
            <TableRow v-if="props.users.data.length === 0">
              <TableDataCell colspan="6" class="px-6 py-4 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center py-6">
                  <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                  <p class="mt-2 text-base font-medium">No users found</p>
                  <p class="text-sm text-gray-500">Add a new user using the button above.</p>
                </div>
              </TableDataCell>
            </TableRow>
          </template>
        </Table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700" v-if="props.users.meta && props.users.meta.total > 0">
              Showing <span class="font-medium">{{ props.users.meta.from }}</span> to 
              <span class="font-medium">{{ props.users.meta.to }}</span> of 
              <span class="font-medium">{{ props.users.meta.total }}</span> results
            </div>
            <Pagination v-if="props.users.meta && props.users.meta.links" :links="props.users.meta.links" @change="handlePageChange" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
