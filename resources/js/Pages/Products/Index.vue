<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import VueMultiselect from 'vue-multiselect';
import { ref, watch, computed, onMounted } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import Paginator from "@/Components/Paginator.vue";




const form = useForm({
    name: "",
    description: "",
    price: "",
    quantity: "",
    min_quantity: "",
    category: null,
    image: null
});

const props = defineProps({
    categories: Array,
    products: Object,
    filters: Object
});

const toast = useToast();
const previewImage = ref(null);
const showDeleteModal = ref(false);
const productToDelete = ref(null);
const sort = ref({ field: props.filters?.sort || 'created_at', direction: props.filters?.direction || 'desc' });
const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');

// DataTable options
const dtOptions = {
  paging: false,
  searching: false,
  info: false,
  ordering: true,
  lengthChange: true,
  pageLength: 10,
  lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
  dom: 'lfrtip',
  responsive: true,
  autoWidth: false,
  scrollX: true,
  scrollCollapse: true
};

// Columns configuration for DataTable
const columns = [
    { 
      data: 'name', 
      title: 'Product',
      className: 'min-w-[120px] font-medium',
      responsivePriority: 1 // Highest priority - always show
    },
    { 
      data: 'description', 
      title: 'Description', 
      className: 'min-w-[200px]',
      responsivePriority: 4, // Lower priority
      render: (data, type, row) => {
        if (type === 'display') {
          return data ? `<div class="line-clamp-2 max-w-xs">${data}</div>` : "<div class=\"text-gray-400\">No description</div>";
        }
        return data;
      }
    },
    { 
      data: null, 
      title: 'Image', 
      orderable: false, 
      className: 'text-center',
      responsivePriority: 3, // Medium priority
      render: (data, type, row) => {
        if (type === 'display') {
          const imgSrc = row.image ? `/storage/${row.image}` : '/images/placeholder-product.png';
          return `<img src="${imgSrc}" alt="${row.name}" class="w-12 h-12 object-cover rounded-md mx-auto">`;
        }
        return '';
      }
    },
    { 
      data: 'price', 
      title: 'Price', 
      className: 'text-right whitespace-nowrap min-w-[100px]',
      responsivePriority: 2, // High priority
      render: (data, type, row) => {
        if (type === 'display' || type === 'filter') {
          return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'MRU'
          }).format(data);
        }
        return data;
      }
    },
    { 
      data: 'quantity', 
      title: 'Stock', 
      className: 'text-center min-w-[80px]',
      responsivePriority: 2, // High priority
      render: (data, type, row) => {
        if (type === 'display') {
          const minQuantity = parseInt(row.min_quantity) || 0;
          const quantity = parseInt(data) || 0;
          const colorClass = quantity <= minQuantity ? 'text-red-600 font-medium' : 'text-green-600 font-medium';
          return `<span class="${colorClass}">${quantity}</span>`;
        }
        return data;
      }
    },
    { 
      data: 'category', 
      title: 'Category', 
      className: 'min-w-[120px]',
      responsivePriority: 3, // Medium priority
      render: (data, type, row) => {
        if (type === 'display') {
          return data ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${data.name}</span>` : '';
        }
        return data ? data.name : '';
      }
    },
    { 
      data: null, 
      title: 'Actions', 
      orderable: false, 
      className: 'min-w-[150px]',
      responsivePriority: 1, // Highest priority - always show
      render: (data, type, row) => {
        if (type === 'display') {
          return `
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 justify-end">
                <a
                    href="/products/${row.id}/edit"
                    class="edit-btn inline-flex items-center gap-1 px-2 sm:px-3 py-1 sm:py-2 border border-blue-600 text-blue-700 bg-blue-50 hover:bg-blue-100 hover:text-blue-900 rounded-md font-medium text-sm shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-400"
                    title="Edit"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="hidden sm:inline">Edit</span>
                </a>
                <button
                    class="delete-btn inline-flex items-center gap-1 px-2 sm:px-3 py-1 sm:py-2 border border-red-600 text-red-700 bg-red-50 hover:bg-red-100 hover:text-red-900 rounded-md font-medium text-sm shadow-sm transition focus:outline-none focus:ring-2 focus:ring-red-400"
                    title="Delete"
                    data-id="${row.id}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span class="hidden sm:inline">Delete</span>
                </button>
            </div>
          `;
        }
        return '';
      }
    }
];

// Keep the original tableHeaders for reference
const tableHeaders = computed(() => [
    { label: 'Product', field: 'name', sortable: true },
    { label: 'Description', field: 'description', sortable: false },
    { label: 'Image', field: null, sortable: false },
    { label: 'Price', field: 'price', sortable: true },
    { label: 'Stock', field: 'quantity', sortable: true },
    { label: 'Category', field: null, sortable: false },
    { label: 'Actions', field: null, sortable: false }
]);

// Watch for filter changes
watch([search, selectedCategory, sort], debounce(() => {
    router.get(route('products.index'), {
        search: search.value,
        category: selectedCategory.value,
        sort: sort.value.field,
        direction: sort.value.direction
    }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300), { deep: true });

const handleSort = (field) => {
    if (!field || !tableHeaders.value.find(header => header.field === field)?.sortable) return;

    if (sort.value.field === field) {
        sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value.field = field;
        sort.value.direction = 'asc';
    }
};

const getSortIcon = (field) => {
    if (!field || sort.value.field !== field) return 'none';
    return sort.value.direction === 'asc' ? 'asc' : 'desc';
};

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        previewImage.value = URL.createObjectURL(file);
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MRU'
    }).format(price);
};
//get image link
const getImageLink = (image) => {
    return image ? '/storage/' + image : '/images/placeholder-product.png';
};
// delete product logic

const confirmDelete = (productId) => {
  productToDelete.value = productId;
  showDeleteModal.value = true;
};

const deleteProduct = () => {
  form.delete(route('products.destroy', productToDelete.value), {
    onSuccess: () => {
      toast.success('Product deleted successfully');
      showDeleteModal.value = false;
      productToDelete.value = null;
    },
    onError: () => {
      toast.error('Failed to delete product');
      showDeleteModal.value = false;
      productToDelete.value = null;
    },
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  productToDelete.value = null;
};

// Add event listeners after the DataTable is initialized
onMounted(() => {
    // Need to wait for the table to be fully initialized
    setTimeout(() => {
        // Event delegation for delete buttons
        document.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                const productId = deleteBtn.getAttribute('data-id');
                if (productId) {
                    confirmDelete(productId);
                }
            }
        });
    }, 500); // Small delay to ensure the DataTable is fully rendered
});

</script>

<template>
  <AuthLayout>
    <Head title="Product Management" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header with stats -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Product Management</h1>
          <p class="text-gray-600 mt-1">Manage your product inventory</p>
        </div>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-3 bg-blue-50/80 px-4 py-2 rounded-lg border border-blue-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-medium text-blue-800">
              Total Products: <span class="font-semibold">{{ props.products.data.length }}</span>
            </span>
          </div>
          <Link
            :href="route('products.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Product
          </Link>
        </div>
      </div>

      <!-- Search and Filter Card -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-xs mb-6">
        <div class="p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
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
                  placeholder="Search products..."
                />
              </div>
            </div>

            <!-- Category Filter -->
            <div class="w-full sm:w-64">
              <select
                v-model="selectedCategory"
                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
              >
                <option value="">All Categories</option>
                <option v-for="category in props.categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            <!-- low stock filter -->
            <div class="w-full sm:w-64">
              <select
                v-model="selectedCategory"
                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
              >
                <option value="">All Stocks</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Table Container -->      
      <div class="bg-white rounded-2xl border border-gray-100 shadow-lg w-full p-4 sm:p-6">
        <div class="w-full overflow-x-auto">
          <DataTable
            :data="props.products.data"
            :columns="columns"
            class="display w-full text-sm sm:text-base text-left text-gray-800 border border-gray-300 rounded-lg table-responsive"
            :options="dtOptions"
          >
            <template #cell-actions="{ row }">
              <div class="flex items-center gap-3 justify-end">
                <a
                  :href="`/products/${row.id}/edit`"
                  class="inline-flex items-center gap-2 px-3 py-2 border border-blue-600 text-blue-700 bg-blue-50 hover:bg-blue-100 hover:text-blue-900 rounded-md font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-400"
                  title="Edit"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Edit
                </a>
                <button
                  class="delete-btn inline-flex items-center gap-2 px-3 py-2 border border-red-600 text-red-700 bg-red-50 hover:bg-red-100 hover:text-red-900 rounded-md font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-red-400"
                  title="Delete"
                  :data-id="row.id"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Delete
                </button>
              </div>
            </template>
          </DataTable>

          <!-- Custom Pagination -->
          <div class="mt-6">
            <Paginator 
              :meta="props.products.meta || props.products" 
              showInfo 
              size="md" 
              align="center" 
              class="w-full" 
            />
          </div>
        </div>
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
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Product</h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this product? This action cannot be undone.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <PrimaryButton

                @click="deleteProduct"
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
  </AuthLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 150ms ease-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
