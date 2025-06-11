<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import Pagination from "@/Components/Pagination.vue";
import { formatPrice } from "@/utils/format.js";

// Props from backend
const props = defineProps({
  products: Object,
  filters: {
    type: Object,
    default: () => ({})
  }
});

// Reactive filters
const searchQuery = ref(props.filters.search || '');
const sortField = ref(props.filters.sort || '');
const sortDirection = ref(props.filters.direction || '');

// Table headers
const tableHeaders = computed(() => [
  { label: 'Product', field: 'name', sortable: true },
  { label: 'Category', field: 'category', sortable: false },
  { label: 'Price', field: 'price', sortable: true },
  { label: 'Quantity', field: 'quantity', sortable: true },
  { label: 'Stock', field: 'stock', sortable: false },
]);

// Debounced search handler
const debouncedSearch = debounce(() => {
  fetchData();
}, 300);

watch(searchQuery, () => {
  debouncedSearch();
});

// Sorting handler
const handleSort = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
  fetchData();
};

// Fetch data with filters
const fetchData = (url = route('products.lowStock')) => {
  router.get(url, {
    search: searchQuery.value,
    sort: sortField.value,
    direction: sortDirection.value,
  }, {
    preserveScroll: true,
    preserveState: true,
    replace: true
  });
};

// Pagination
const handlePageChange = (url) => {
  fetchData(url);
};

// Total value
const totalValue = computed(() => {
  return props.products.data.reduce((total, product) => {
    return total + (product.price * product.quantity);
  }, 0);
});
</script>

<template>
  <AuthLayout>
    <Head title="Produits en faible stock" />

    <div class="max-w-7xl mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold">Produits en faible stock</h1>
          <p class="text-sm text-gray-500">Produits avec une quantité de 10 ou moins</p>
        </div>
        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded text-sm">
          Valeur totale du stock faible : {{ formatPrice(totalValue) }}
        </div>
      </div>

      <div class="mb-6">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Rechercher des produits par nom..."
          class="w-full sm:w-1/2 px-4 py-2 border rounded shadow-sm text-sm"
        />
      </div>

      <div class="bg-white shadow rounded overflow-hidden">
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
            <TableRow v-for="product in products.data" :key="product.id">
              <TableDataCell>
                <div>
                  <div class="font-medium">{{ product.name }}</div>

                </div>
              </TableDataCell>
              <TableDataCell>
                <div v-for="cat in product.categories" :key="cat.id" class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded inline-block mr-1">
                  {{ cat.name }}
                </div>
              </TableDataCell>
              <TableDataCell>{{ formatPrice(product.price) }}</TableDataCell>
              <TableDataCell>
                <span :class="{
                  'bg-red-100 text-red-800': product.quantity <= 5,
                  'bg-yellow-100 text-yellow-800': product.quantity > 5 && product.quantity <= 10
                }" class="text-xs font-semibold px-2 py-1 rounded">
                  {{ product.quantity }}
                </span>
              </TableDataCell>
              <TableDataCell>{{ product.stock  }}</TableDataCell>
            </TableRow>

            <TableRow v-if="products.data.length === 0">
              <TableDataCell colspan="5" class="text-center text-gray-500 py-4">
                Aucun produit trouvé.
              </TableDataCell>
            </TableRow>
          </template>
        </Table>

        <div v-if="products.meta.total > 0" class="p-4 border-t">
          <div class="flex justify-between items-center text-sm">
            <div>Affichage de {{ products.meta.from }} à {{ products.meta.to }} sur {{ products.meta.total }}</div>
            <Pagination :links="products.meta.links" @change="handlePageChange" />
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>
