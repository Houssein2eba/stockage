<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import {Head, Link} from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { useToast } from "vue-toastification"
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import VueMultiselect from 'vue-multiselect'
const props = defineProps({
    permissions: {
        type: Array,
        required: true
    }
});


const toast = useToast();
const form = useForm({
    name: '',
    permissions: []
});

const submit = () => {
    form.post(route('roles.store'), {
        onSuccess: () => {
            toast.success('Role created successfully');
            form.reset();
        },
        onError: (errors) => {
            toast.error('Failed to create role');
            console.error(errors);
        }
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Create Role" />
       
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <H1>Create Role</H1>
                    <P>Create a new role with permissions</P>
                </div>
                <div>
                    <Link
                        :href="route('roles.index')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Roles
                    </Link>
                    </div>
            </div>
<div class="m-auto w-3/4 sm:w-1/2">
            <div class="bg-gray-200 rounded-lg shadow-sm">
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
                        <VueMultiselect
                            v-model="form.permissions"
                            :options="permissions"
                            :multiple="true"
                            :close-on-select="false"
                            :clear-on-select="false"
                            :searchable="true"
                            track-by="id"
                            label="name"
                            :placeholder="permissions.length > 0 ? 'Select Permissions' : 'No permissions available'"

                        />

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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Create Role
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
    </AuthLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
