<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from "vue-toastification"
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    role: {
        type: Object,
        required: true
    },
    permissions: {
        type: Array,
        required: true
    }
});

const toast = useToast();
const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map(p => p.id)
});

const submit = () => {
    form.put(route('roles.update', props.role.id), {
        onSuccess: () => {
            toast.success('Role updated successfully');
        },
        onError: () => {
            toast.error('Failed to update role');
        }
    });
};
</script>

<template>
    <AuthLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Role</h1>
                    <p class="text-gray-600 mt-1">Modify role details and permissions</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Role Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel value="Permissions" />
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div
                                v-for="permission in permissions"
                                :key="permission.id"
                                class="flex items-start"
                            >
                                <div class="flex items-center h-5">
                                    <input
                                        :id="'permission-' + permission.id"
                                        type="checkbox"
                                        :value="permission.id"
                                        v-model="form.permissions"
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        aria-labelledby="'permission-' + permission.id"
                                        aria-describedby="'permission-' + permission.id"
                                    />
                                </div>
                                <div class="ml-3 text-sm">
                                    <label :for="'permission-' + permission.id" class="font-medium text-gray-700">
                                        {{ permission.name }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.permissions" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton
                            type="submit"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="!form.processing" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Role
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
    </AuthLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
