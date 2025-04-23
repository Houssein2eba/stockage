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

defineOptions({
    layout:AuthLayout
});
const props = defineProps({
  users: {
    type: Array,
    default: () => [],
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

// Watch for search and sort changes
watch([search, sort], debounce(() => {
  router.get('/users', {
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
};

const getSortIcon = (field) => {
  if (!field || sort.value.field !== field) return 'none';
  return sort.value.direction === 'asc' ? 'asc' : 'desc';
};
</script>


  <template>
  <Head title="Users index" />

  <div class="flex justify-between items-center mb-6">
        <input type="text" v-model="search" placeholder="Search..." class="border border-gray-300 rounded-md px-4 py-2">
        <h1 class="text-2xl font-bold">Employees</h1>
        <Link
            :href="route('users.create')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>



          Add User</Link>



    </div>

    <div class=" max-w-7xl m-auto py-4">

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
          </template>
        </Table>
      </div>
    </div>

</template>
