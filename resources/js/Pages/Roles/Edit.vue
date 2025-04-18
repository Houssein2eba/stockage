<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import VueMultiselect from 'vue-multiselect';
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { defineOptions } from 'vue'
defineOptions({
    layout: AuthLayout
})

const props = defineProps({
    role: Object,
    permissions: Array
})

const form = useForm({
    id: props.role.id,
    name: props.role.name,
    permissions: props.role.permissions ? props.role.permissions.map(p => ({
        id: p.id,
        name: p.name
    })) : []
});

const toast = useToast();

const saveRole = () => {
    // Transform permissions array to just IDs before sending
    const payload = {
        ...form.data(),
        permissions: form.permissions
    };

    form.transform(() => payload).put(route('roles.update', props.role.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Role updated successfully');
        },
        onError: () => {
            toast.error('Failed to update role');
        }
    });
}
</script>

<template>
  <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Edit Role</h2>

        <form @submit.prevent="saveRole">
          <div class="mb-4">
            <InputLabel for="role-name" value="Role Name" />
            <input
              id="role-name"
              v-model="form.name"
              type="text"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              :class="{ 'border-red-500': form.errors.name }"
            >
            <InputError :message="form.errors.name" class="mt-2" />
          </div>

          <div class="mb-6">
            <InputLabel for="permissions" value="Permissions" />
            <VueMultiselect
              v-model="form.permissions"
              :options="permissions"
              :multiple="true"
              :close-on-select="false"
              placeholder="Select permissions"
              label="name"
              track-by="id"
              :class="{ 'border-red-500': form.errors.permissions }"
            />
            <InputError :message="form.errors.permissions" class="mt-2" />
            
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
