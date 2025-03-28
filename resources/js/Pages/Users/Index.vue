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
import { defineProps, ref } from 'vue';

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
    }
  }
)
    
}
</script>


  <template>
  <Head title="Users index" />

  <div class="flex justify-between items-center mb-6">
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
      <div class="flex justify-between">
        <h1>Users Index Page</h1>
        <!-- <Link
          :href="route('users.create')"
          class="px-3 py-2 text-white font-semibold bg-indigo-500 hover:bg-indigo-700 rounded"
          >New User</Link
        > -->
      </div>
      <div class="mt-6">
        <!-- <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                ID
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Role
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </th>
              <th scope="col" colspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Action
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.id" class="border-b">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ user.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ user.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ user.roles.map((role) => role.name).join(', ') }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ user.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <Link
                  :href="route('users.edit', user.id)"
                  class="text-green-400 hover:text-green-600"
                  >Edit</Link
                >
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <button @click="showConfirmDeleteUserModal = true" class="text-red-400 hover:text-red-600">Delete</button>
                
              </td>
            </tr>
          </tbody>
        </table> -->
        <Table>
          <template #header>
            <TableRow>
              <TableHeaderCell>ID</TableHeaderCell>
              <TableHeaderCell>Name</TableHeaderCell>
              <TableHeaderCell>Role</TableHeaderCell>
              <TableHeaderCell>Email</TableHeaderCell>
              <TableHeaderCell>Action</TableHeaderCell>
            </TableRow>
          </template>
          <template #default>
            <TableRow v-for="user in users" :key="user.id" class="border-b">
              <TableDataCell>{{ user.id }}</TableDataCell>
              <TableDataCell>{{ user.name }}</TableDataCell>
              <TableDataCell>{{ user.roles.map((role) => role.name).join(', ') }}</TableDataCell>
              <TableDataCell>{{ user.email }}</TableDataCell>
              <TableDataCell class="space-x-4">
                <Link
                  :href="route('users.edit', user.id)"
                  class="text-green-400 hover:text-green-600"
                  >Edit</Link
                >
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
            </TableRow>
          </template>
        </Table>
      </div>
    </div>
  
</template>
