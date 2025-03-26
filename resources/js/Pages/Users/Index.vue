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
                  :href="route('users.create', user.id)"
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
