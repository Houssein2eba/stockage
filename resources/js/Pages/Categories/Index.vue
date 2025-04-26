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
import { ref, watch, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { debounce } from 'lodash';
import Pagination from "@/Components/Pagination.vue";




const props = defineProps({
    categories: {
        type: Object,
        required: true,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    },

    categories_count: {
        type: Number,
        default: 0
    }
});

const search = ref('');
const sort = ref({ field: props.filters?.sort || 'created_at', direction: props.filters?.direction || 'desc' });
const page = ref(props.categories?.meta?.current_page || 1);

// Watch for search changes only, not page or sort as they have direct handlers
watch([search], debounce(() => {
    router.get(route('categories.index'), {
        search: search.value,
        sort: sort.value.field,
        direction: sort.value.direction,
        page: 1 // Reset to page 1 when searching
    }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300));

// Computed property for table headers with sorting
const tableHeaders = computed(() => [
    { label: 'Name', field: 'name', sortable: true },
    { label: 'Description', field: 'description', sortable: true },
    { label: 'Products', field: 'products_count', sortable: true },
    { label: 'Actions', field: null, sortable: false }
]);

const handleSort = (field) => {
    if (!field || !tableHeaders.value.find(header => header.field === field)?.sortable) return;

    if (sort.value.field === field) {
        sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value.field = field;
        sort.value.direction = 'asc';
    }

    // Keep the current page when sorting
    router.get(route('categories.index'), {
        search: search.value,
        sort: sort.value.field,
        direction: sort.value.direction,
        page: page.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getSortIcon = (field) => {
    if (!field || sort.value.field !== field) return 'none';
    return sort.value.direction === 'asc' ? 'asc' : 'desc';
};

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
            showDeleteModal.value = false;
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
        },
    });
};

const editForm = useForm({
    id: null,
    name: "",
    description: ""
});

const showEditModal = ref(false);

const openEditModal = (category) => {
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
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
        },
    });
};

// Handle pagination link clicks
const handlePageChange = (url) => {
    if (!url) return;

    // Extract page number from URL
    const urlObj = new URL(url);
    const pageParam = urlObj.searchParams.get('page');

    if (pageParam) {
        page.value = parseInt(pageParam);

        router.get(route('categories.index'), {
            search: search.value,
            sort: sort.value.field,
            direction: sort.value.direction,
            page: page.value
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
};
</script>

<template>
    <AuthLayout>
        <Head title="Categories" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header with stats -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div>
                    <H1>Categories</H1>
                    <P>Manage your product categories</P>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 bg-blue-50/80 px-4 py-2 rounded-lg border border-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span class="text-sm font-medium text-blue-800">
                            Total Categories: <span class="font-semibold">{{ categories_count }}</span>
                        </span>
                    </div>
                    <Link
                        :href="route('categories.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add New Category
                    </Link>
                </div>
            </div>

            <!-- Search Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs mb-6">
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    v-model="search"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Search categories..."
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs m-auto">
                <div class="w-full overflow-x-auto">
                    <Table >
                        <template #header>
                            <TableRow>
                                <TableHeaderCell
                                    v-for="header in tableHeaders"
                                    :key="header.field || header.label"
                                    :class="[
                                        header.sortable ? 'cursor-pointer hover:bg-gray-100' : '',
                                        'px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'
                                    ]"
                                    @click="handleSort(header.field)"
                                >
                                    <div class="flex items-center space-x-1">
                                        <span>{{ header.label }}</span>
                                        <span v-if="header.sortable" class="flex flex-col">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3"
                                                :class="{'text-blue-600': getSortIcon(header.field) === 'asc'}"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                            </svg>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3"
                                                :class="{'text-blue-600': getSortIcon(header.field) === 'desc'}"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </TableHeaderCell>
                            </TableRow>
                        </template>
                        <template #body>
                            <TableRow v-for="category in categories.data" :key="category.id" class="hover:bg-gray-50/50 transition-colors">
                                <TableDataCell class="px-4 sm:px-6 py-4">
                                    <span class="font-medium text-gray-900">{{ category.name }}</span>
                                </TableDataCell>
                                <TableDataCell class="px-4 sm:px-6 py-4">
                                    <div class="text-gray-600 truncate max-w-xs">
                                        {{ category.description || "---" }}
                                    </div>
                                </TableDataCell>
                                <TableDataCell class="px-4 sm:px-6 py-4 text-center">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        {{ category.products_count }}
                                    </span>
                                </TableDataCell>
                                <TableDataCell class="px-4 sm:px-6 py-4">
                                    <div class="flex items-center justify-end gap-2 sm:gap-3">
                                        <button
                                            @click="openEditModal(category)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors flex items-center gap-1 whitespace-nowrap"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span class="hidden sm:inline">Edit</span>
                                        </button>
                                        <button
                                            @click="confirmDelete(category.id)"
                                            class="text-red-600 hover:text-red-900 transition-colors flex items-center gap-1 whitespace-nowrap"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span class="hidden sm:inline">Delete</span>
                                        </button>
                                    </div>
                                </TableDataCell>
                            </TableRow>
                            <TableRow v-if="categories.data.length === 0">
                                <TableDataCell colspan="4" class="px-4 sm:px-6 py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <p class="mt-2">No Categories found</p>
                                        <p class="text-sm text-gray-400">Add your first category using the create button above</p>
                                    </div>
                                </TableDataCell>
                            </TableRow>
                        </template>
                    </Table>

                </div>
                 <!-- Pagination  -->
                 <div class="flex items-center justify-between mt-4">
              <div class="text-sm text-gray-700" v-if="categories_count > 0">
                Showing <span class="font-medium">{{ categories.meta.from }}</span> to
                <span class="font-medium">{{ categories.meta.to }}</span> of
                <span class="font-medium">{{ categories.meta.total }}</span> results
              </div>
                <Pagination :links="categories.meta.links" @change="handlePageChange" />
               </div>
            </div>

            <!-- Delete confirmation modal -->
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
        </div>
    </AuthLayout>
</template>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 150ms ease-out;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
