<template>
  <AuthLayout>
    <Head title="Create Category" />


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8 flex items-center justify-between">
        <div>
          <H1 >Create New Category</H1>
          <P>Add a new category to organize your Products</P>
          

        </div>
        <div>
            <Link
                :href="route('categories.index')"
                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Categories
            </Link>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow lg:w-1/2 sm:w-1/3 mx-auto">
        <div class="px-4  rounded-md py-5 sm:p-6">
          <form @submit.prevent="form.post(route('categories.store'), {
            onSuccess: () => {
              toast.success('Category created successfully');
              router.visit(route('categories.index'));
            },
            onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
        }
          })" class="space-y-6">
            <div>
              <InputLabel for="name" value="Category Name" class="mb-1.5" />
              <TextInput
                id="name"
                type="text"
                class="w-full"
                v-model="form.name"
                placeholder="Enter category name"
                :class="{ 'border-red-500': form.errors.name }"
              />
              <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <div>
              <InputLabel for="description" value="Description" class="mb-1.5" />
              <textarea
                id="description"
                rows="3"
                class="w-full rounded-md ring-1 ring-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                v-model="form.description"
                placeholder="Enter category description"
                :class="{ 'border-red-500': form.errors.description }"
              ></textarea>
              <InputError class="mt-1.5" :message="form.errors.description" />
            </div>

            <div class="flex justify-end space-x-3">
              <Link
                :href="route('categories.index')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
              >
                Cancel
              </Link>
              <PrimaryButton
                class="px-4 py-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                <span v-if="!form.processing">Create Category</span>
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

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const form = useForm({
  name: '',
  description: ''
});
</script>
