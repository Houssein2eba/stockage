<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

defineOptions({
    layout: AuthLayout
})

const props = defineProps({
    role: {
        type: Object,
        default: () => ({}),
    },
    permissions: {
        type: Array,
        default: () => [],
    }
})

const form = useForm({
    name: props.role?.name || '',
    permissions: props.role?.permissions?.map(p => p.name) || []
})

const toast = useToast();

const saveRole = () => {
    form.put(route('roles.update', props.role.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Role updated successfully');
        },
        onError: () => {
            toast.error('Failed to update role');
        }
    });
}

const togglePermission = (permissionName) => {
    if (form.permissions.includes(permissionName)) {
        form.permissions = form.permissions.filter(p => p !== permissionName);
    } else {
        form.permissions.push(permissionName);
    }
}
</script>

<template>
  <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Edit Role</h2>
        
        <form @submit.prevent="saveRole">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="role-name">
              Role Name
            </label>
            <input
              id="role-name"
              v-model="form.name"
              type="text"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              :class="{ 'border-red-500': form.errors.name }"
            >
            <p v-if="form.errors.name" class="text-red-500 text-xs italic mt-1">
              {{ form.errors.name }}
            </p>
          </div>

          <div class="mb-6">
            <h3 class="block text-gray-700 text-sm font-bold mb-2">Permissions</h3>
            <div class="max-h-60 overflow-y-auto border rounded-md p-2"> <!-- Scrollable container -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div v-for="permission in permissions" :key="permission.id" class="flex items-center">
                  <input
                    :id="`permission-${permission.id}`"
                    :checked="form.permissions.includes(permission.name)"
                    @change="togglePermission(permission.name)"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-700">
                    {{ permission.name }}
                  </label>
                </div>
              </div>
            </div>
            <p v-if="form.errors.permissions" class="text-red-500 text-xs italic mt-1">
              {{ form.errors.permissions }}
            </p>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="form.processing"
              class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
            >
              <span v-if="form.processing">Saving...</span>
              <span v-else>Save</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>