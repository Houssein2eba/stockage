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
const search = ref('');
watch(search, debounce((value) => {
  router.get(`/users`, { search: value }, {
    preserveState: true,
    preserveScroll: true,
  });
}, 500));
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

              <TableHeaderCell>Name</TableHeaderCell>
              <TableHeaderCell>Role</TableHeaderCell>
              <TableHeaderCell>Phone</TableHeaderCell>
              <TableHeaderCell>Email</TableHeaderCell>
              <TableHeaderCell :colspan=2 >Action</TableHeaderCell>
            </TableRow>
          </template>
          <template #default>
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
