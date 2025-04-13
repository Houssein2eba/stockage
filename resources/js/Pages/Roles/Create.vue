<script setup>
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue';
import {useToast} from "vue-toastification";
import InputError from "@/Components/InputError.vue";
import VueMultiselect from 'vue-multiselect';
defineOptions({
    layout:AuthLayout
})

const props = defineProps({
  permissions: Array
})


const toast = useToast();
const form = useForm({
  name: '',
  permissions: []
})
console.log(props.permissions)
const saveRole = () => {
  form.post(route('roles.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Role created successfully');
      form.reset();
    }
  })
}
</script>

<template>
  <div class="fixed inset-0  bg-opacity-50 flex items-center justify-center p-4 ">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Create Role</h2>
        
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
            <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-2"> -->
              <!-- <div v-for="permission in permissions" :key="permission.id" class="flex items-center">
                <input
                  :id="`permission-${permission.id}`"
                  v-model="form.permissions"
                  type="checkbox"
                  :value="permission.name"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-700">
                  {{ permission.name }}
                </label>
                <inputError :message="form.errors.permissions" class="mt-2" />
              </div> -->
              <div class="mb-4">
                <InputLabel for="permissions"  value="Permissions"/>
                <VueMultiselect
  v-model="form.permissions"
  :options="permissions"
  :multiple="true"
  :close-on-select="true"
  placeholder="Pick some"
  label="name"
  track-by="id"
  class="w-full"
/>
                <p v-if="form.errors.permissions" class="text-red-500 text-xs italic mt-1">
                  {{ form.errors.permissions }}
                </p>
              </div>
            <!-- </div> -->
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
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

