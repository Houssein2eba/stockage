<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";

const form = useForm({
    name: "",
    description: ""
});

const props = defineProps({
    categories: {
        type: Array,
        required: true,
        default: () => []
    }
});

const toast = useToast();
</script>

<template>
  <AuthLayout>
    <Head title="Categories" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8 pt-6">
        <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
      </div>

      <!-- Two-column layout -->
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Main content area (table) -->
        <div class="flex-1">
          <div class="bg-white shadow rounded-lg overflow-hidden">
            <Table class="w-full">
              <thead class="bg-gray-50">
                <TableRow>
                  <TableHeaderCell class="w-1/3">Name</TableHeaderCell>
                  <TableHeaderCell class="w-2/5">Description</TableHeaderCell>
                  <TableHeaderCell class="w-1/5 text-center">Products</TableHeaderCell>
                  <TableHeaderCell class="w-1/5 text-right">Actions</TableHeaderCell>
                </TableRow>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <TableRow v-for="category in categories" :key="category.id" class="hover:bg-gray-50">
                  <TableDataCell>
                    <span class="font-medium text-gray-900">{{ category.name }}</span>
                  </TableDataCell>
                  <TableDataCell class="text-gray-600">
                    {{ category.description || "---" }}
                  </TableDataCell>
                  <TableDataCell class="text-center">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                      {{ category.products_count }}
                    </span>
                  </TableDataCell>
                  <TableDataCell class="text-right">
                    <div class="flex justify-end space-x-2">
                      <Link 
                        :href="route('categories.index')"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        Edit
                      </Link>
                      <button 
                        @click="confirmDelete(category)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Delete
                      </button>
                    </div>
                  </TableDataCell>
                </TableRow>
                <TableRow v-if="categories.length === 0">
                  <TableDataCell colspan="4" class="text-center py-4 text-gray-500">
                    No categories found
                  </TableDataCell>
                </TableRow>
              </tbody>
            </Table>
          </div>
        </div>

        <!-- Sidebar form -->
        <div class="md:w-80">
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Add New Category</h2>
            <form @submit.prevent="form.post(route('categories.store'), {
              onSuccess: () => {
                toast.success('Category created successfully');
                form.reset();
              },
              onError: () => {
                toast.error('Failed to create category');
              },
            })">
              <div class="mb-4">
                <InputLabel for="name" value="Name" class="block text-sm font-medium text-gray-700 mb-1" />
                <TextInput
                  id="name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  v-model="form.name"
                  autofocus
                  autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>
              <div class="mb-4">
                <InputLabel for="description" value="Description" class="block text-sm font-medium text-gray-700 mb-1" />
                <textarea
                  id="description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  v-model="form.description"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.description" />
              </div>
              <div class="flex justify-end">
                <PrimaryButton
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 focus:ring-offset-2"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  Add New Category
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>