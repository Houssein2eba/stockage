<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import Pagination from "@/Components/Pagination.vue";
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { formatPrice } from "@/utils/format.js";
import { formatDate } from '@/utils/formatDate.js';

const toast = useToast();

const props = defineProps({
    stocks: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    categories: {
        type: Array,
        default: () => []
    }
});

// Search and sort state
const globalSearchQuery = ref(props.filters.search || '');
const stockSearchQueries = ref({});
const sortField = ref(props.filters.sort || '');
const sortDirection = ref(props.filters.direction || '');

// Delete modal state
const showDeleteModal = ref(false);
const productToDelete = ref(null);
const deleteForm = useForm({});

// Initialize stock search queries
props.stocks.data.forEach(stock => {
    stockSearchQueries.value[stock.id] = props.filters.stock_search?.[stock.id] || '';
});

const tableHeaders = computed(() => [
    { label: 'Product', field: 'name', sortable: true },
    { label: 'Category', field: null, sortable: false },
    { label: 'Price', field: 'price', sortable: true },
    { label: 'Quantity', field: 'quantity', sortable: true },
    { label: 'Total Value', field: null, sortable: false },
    { label: 'Edit', field: null, sortable: false },
    { label: 'Delete', field: null, sortable: false }
]);

// Modal functions
const openDeleteModal = (product) => {
    productToDelete.value = product;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    productToDelete.value = null;
};

const deleteProduct = () => {
    if (!productToDelete.value) return;

    deleteForm.delete(route('products.destroy', productToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
            toast.success('Product deleted successfully');
        },
        onError: () => {
            toast.error('Failed to delete product');
        }
    });
};

// Search handlers
watch(globalSearchQuery, debounce(() => {
    router.get(route('products.index'), {
        search: globalSearchQuery.value,
        stock_search: stockSearchQueries.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300));

const handleStockSearch = debounce((stockId) => {
    router.get(route('products.index'), {
        search: globalSearchQuery.value,
        stock_search: {
            ...stockSearchQueries.value,
            [stockId]: stockSearchQueries.value[stockId]
        },
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

// Sort handler
const handleSort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    router.get(route('products.index'), {
        search: globalSearchQuery.value,
        stock_search: stockSearchQueries.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

// Export functions
const exportExcel = () => {
    window.location.href = route('products.export');
};

const exportSingleExcel = (stockId) => {
    window.location.href = route('products.single_export', { id: stockId });
};

// Helper functions
const calculateTotalValue = (products) => {
    return products?.reduce((total, product) => {
        return total + (product.price * product.quantity);
    }, 0) || 0;
};

// Pagination handlers
const handleProductPageChange = (url, stockId) => {
    if (!url) return;

    router.get(url, {
        search: globalSearchQuery.value,
        stock_search: stockSearchQueries.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const handleStockPageChange = (url) => {
    if (!url) return;

    router.get(url, {
        search: globalSearchQuery.value,
        stock_search: stockSearchQueries.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};
</script>

<template>
  <AuthLayout>
    <Head title="Stock Management" />

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal" maxWidth="md">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Confirm Product Deletion
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          Are you sure you want to delete "{{ productToDelete?.name }}"? This action cannot be undone.
        </p>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeDeleteModal">
            Cancel
          </SecondaryButton>

          <DangerButton
            class="ml-3"
            @click="deleteProduct"
            :disabled="deleteForm.processing"
          >
            <span v-if="!deleteForm.processing">Delete Product</span>
            <span v-else class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Deleting...
            </span>
          </DangerButton>
        </div>
      </div>
    </Modal>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Stock Management</h1>
          <p class="text-sm text-gray-500">Manage your stock locations and inventory</p>
        </div>
        <div class="flex items-center gap-4">
          <Link
            :href="route('products.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Product
          </Link>
          <button
            @click="exportExcel()"
            class="inline-flex items-center py-2 px-4 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export All
          </button>
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
                  v-model="globalSearchQuery"
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Search products across all stocks..."
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stocks List -->
      <div class="space-y-8">
        <div v-for="stock in stocks.data" :key="stock.id" class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
          <!-- Stock Header -->
          <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-blue-600 to-blue-800">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg leading-6 font-medium text-white">
                  {{ stock.name }} - {{ stock.location }}
                </h3>
                <p class="mt-1 text-sm text-blue-100">
                  {{ stock.products_count }} products in stock
                </p>
              </div>
              <div class="flex items-center gap-3">
                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                      :class="{
                        'bg-green-100 text-green-800': stock.status === 'good',
                        'bg-yellow-100 text-yellow-800': stock.status === 'low',
                        'bg-red-100 text-red-800': stock.status === 'empty'
                      }">
                  {{ stock.status }}
                </span>
                <button
                  @click="exportSingleExcel(stock.id)"
                  class="inline-flex items-center py-2 px-4 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Export
                </button>
                <Link
                  :href="route('stocks.edit', stock.id)"
                  class="inline-flex items-center px-3 py-1 bg-white hover:bg-gray-100 text-blue-600 text-xs font-medium rounded-md shadow-sm transition-colors"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Edit
                </Link>
              </div>
            </div>
          </div>

          <!-- Stock Details -->
          <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Location
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ stock.location }}
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Status
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  <span class="capitalize px-2 py-1 rounded-full text-xs font-medium"
                    :class="{
                      'bg-green-100 text-green-800': stock.status === 'good',
                      'bg-yellow-100 text-yellow-800': stock.status === 'low',
                      'bg-red-100 text-red-800': stock.status === 'empty'
                    }">
                    {{ stock.status }}
                  </span>
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Created At
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ formatDate(stock.created_at) }}
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Last Updated
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ formatDate(stock.updated_at) }}
                </dd>
              </div>
            </dl>
          </div>

          <!-- Products Section -->
          <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Products in Stock</h3>
                <p class="mt-1 text-sm text-gray-500">Inventory items currently available in this location</p>
              </div>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input
                  type="text"
                  v-model="stockSearchQueries[stock.id]"
                  @input="handleStockSearch(stock.id)"
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Search products in this stock..."
                />
              </div>
            </div>
          </div>

          <!-- Products Table -->
          <div class="overflow-x-auto">
            <Table>
              <template #header>
                <TableRow>
                  <TableHeaderCell
                    v-for="header in tableHeaders"
                    :key="header.field || header.label"
                    @click="header.sortable ? handleSort(header.field) : null"
                    :class="{'cursor-pointer hover:bg-gray-50': header.sortable}"
                  >
                    <div class="flex items-center">
                      {{ header.label }}
                      <span v-if="header.sortable && sortField === header.field" class="ml-1">
                        {{ sortDirection === 'asc' ? '↑' : '↓' }}
                      </span>
                    </div>
                  </TableHeaderCell>
                </TableRow>
              </template>

              <template #body>
                <TableRow v-for="product in stock.paginated_products?.data" :key="product.id">
                  <TableDataCell>
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          <Link :href="route('products.show', product.id)" class="hover:underline">
                            {{ product.name }}
                          </Link>
                        </div>
                        <div class="text-sm text-gray-500">
                          SKU: {{ product.sku || 'N/A' }}
                        </div>
                      </div>
                    </div>
                  </TableDataCell>
                  <TableDataCell>
                    <div v-for="category in product.categories" :key="category.id"
                         class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mb-1">
                      {{ category.name || 'Uncategorized' }}
                    </div>
                  </TableDataCell>
                  <TableDataCell>
                    {{ formatPrice(product.price) }}
                  </TableDataCell>
                  <TableDataCell>
                    <span :class="{
                      'text-green-600 font-medium': product.quantity > 10,
                      'text-yellow-600 font-medium': product.quantity > 0 && product.quantity <= 10,
                      'text-red-600 font-medium': product.quantity === 0
                    }">
                      {{ product.quantity }}
                    </span>
                  </TableDataCell>
                  <TableDataCell>
                    {{ formatPrice(product.price * product.quantity) }}
                  </TableDataCell>
                  <TableDataCell>
                    <Link :href="route('products.edit', product.id)"
                          class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Edit
                    </Link>
                  </TableDataCell>
                  <TableDataCell>
                    <button
                      @click="openDeleteModal(product)"
                      class="text-red-600 hover:text-red-900 inline-flex items-center"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete
                    </button>
                  </TableDataCell>
                </TableRow>
                <TableRow v-if="!stock.paginated_products?.data?.length">
                  <TableDataCell colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                    No products found in this stock location
                  </TableDataCell>
                </TableRow>

                <TableRow v-if="stock.paginated_products?.data?.length > 0">
                  <TableDataCell colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                    Total Inventory Value:
                  </TableDataCell>
                  <TableDataCell class="px-6 py-3 text-sm font-bold text-gray-900">
                    {{ formatPrice(calculateTotalValue(stock.paginated_products.data)) }}
                  </TableDataCell>
                  <TableDataCell colspan="2"></TableDataCell>
                </TableRow>
              </template>
            </Table>
          </div>

          <!-- Products Pagination -->
          <div v-if="stock.paginated_products?.meta" class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ stock.paginated_products.meta.from || 0 }} to {{ stock.paginated_products.meta.to || 0 }} of {{ stock.paginated_products.meta.total }} results
              </div>
              <Pagination
                :links="stock.paginated_products.meta.links"
                @change="(url) => handleProductPageChange(url, stock.id)"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Stocks Pagination -->
      <div v-if="stocks.meta" class="mt-6 bg-white rounded-lg border border-gray-200 shadow-xs p-4">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ stocks.meta.from || 0 }} to {{ stocks.meta.to || 0 }} of {{ stocks.meta.total }} stocks
          </div>
          <Pagination
            :links="stocks.meta.links"
            @change="handleStockPageChange"
          />
        </div>
      </div>
    </div>
  </AuthLayout>
</template>
