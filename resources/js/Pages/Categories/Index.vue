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
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    description: "",
});

const props = defineProps({
    categories: {
        type: Array,
        required: true,
        default: () => []
    }
});

const toast = useToast();
const showDeleteModal = ref(false);
const categoryToDelete = ref(null);
const confirmDelete = (category) => {
    categoryToDelete.value = category;
    showDeleteModal.value = true;
};
const deleteCategory = () => {
    router.delete(`/categories/${categoryToDelete.value}`, {
        onSuccess: () => {
            toast.success("Category deleted successfully");
        },
        onError: () => {
            toast.error("Failed to delete category");
        },
    });
    showDeleteModal.value = false;
};

const editForm = useForm({
    id: null,
    name: "",
    description: ""
});

const showEditModal = ref(false);
const currentCategory = ref(null);

const openEditModal = (category) => {
    currentCategory.value = category;
    editForm.id = category.id;
    editForm.name = category.name;
    editForm.description = category.description || "";
    showEditModal.value = true;
};

const updateCategory = () => {
    editForm.put(route('categories.update', editForm.id), {
        onSuccess: () => {
            toast.success("Category updated successfully");
            showEditModal.value = false;
            editForm.reset();
        },
        onError: () => {
            toast.error("Failed to update category");
        },
    });
};
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
                      <button @click="openEditModal(category)" class="text-blue-600 hover:text-blue-900">
                        Edit
                      </button>
                      <button
                        @click="confirmDelete(category.id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Delete
                      </button>

                    </div>
                  </TableDataCell>
                </TableRow>
                <TableRow v-if="categories.length === 0">
                  <TableDataCell colspan="4" class="text-center py-4 text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="mt-2">No Categories found</p>
                        <p class="text-sm text-gray-400">Add your first category using the form</p>
                      </div>
                  </TableDataCell>
                </TableRow>
              </tbody>
            </Table>
          </div>
        </div>
 <!-- ModalConfirmDelete -->
 <Transition name="fade">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Category</h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this category? This action cannot be undone.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <PrimaryButton

                @click="deleteCategory"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                :disabled="form.processing"
              >
                <span v-if="!form.processing">Delete</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Deleting...
                </span>
              </PrimaryButton>
              <button

                @click="showDeleteModal = false"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-700 text-base font-medium text-white  focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto sm:text-sm"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
       <!-- Edit Category Modal -->
  <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
      <div class="flex justify-between items-start mb-4">
        <h3 class="text-lg font-bold text-gray-900">Edit Category</h3>
        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="updateCategory">
        <div class="mb-4">
          <InputLabel for="edit-name" value="Name" class="block text-sm font-medium text-gray-700 mb-1" />
          <TextInput
            id="edit-name"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            v-model="editForm.name"
            required
            autofocus
            :class="{ 'border-red-500': editForm.errors.name }"
          />
          <InputError class="mt-2" :message="editForm.errors.name" />
        </div>

        <div class="mb-4">
          <InputLabel for="edit-description" value="Description" class="block text-sm font-medium text-gray-700 mb-1" />
          <textarea
            id="edit-description"
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            v-model="editForm.description"
            :class="{ 'border-red-500': editForm.errors.description }"
          ></textarea>
          <InputError class="mt-2" :message="editForm.errors.description" />
        </div>

        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="showEditModal = false"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <PrimaryButton
            type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 focus:ring-offset-2"
            :class="{ 'opacity-25': editForm.processing }"
            :disabled="editForm.processing"
          >
            Update Category
          </PrimaryButton>
        </div>
      </form>
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
                  :class="{ 'border-2 border-red-500': form.errors.name  }"
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
                  :class="{ 'border border-red-500': form.errors.description }"
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
