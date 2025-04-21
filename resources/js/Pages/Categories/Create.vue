<template>
  <AuthLayout>
    <Head title="Create Category" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Create New Category</h1>
          <p class="mt-1 text-sm text-gray-600">Add a new category to organize your products</p>
        </div>
        <Link
          :href="route('categories.index')"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
          </svg>
          Back to Categories
        </Link>
      </div>

      <div class="bg-white rounded-lg shadow max-w-3xl mx-auto">
        <div class="px-4 py-5 sm:p-6">
          <form @submit.prevent="form.post(route('categories.store'), {
            onSuccess: () => {
              toast.success('Category created successfully');
              router.visit(route('categories.index'));
            },
            onError: () => {
              toast.error('Failed to create category');
            },
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
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
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
