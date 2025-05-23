<template>
  <AuthLayout>
    <Head :title="`Stock Details - ${stock.name}`" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Stock Details</h1>
          <p class="text-sm text-gray-500">Viewing stock location information</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('stocks.index')"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
            preserve-scroll
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Stocks
          </Link>

          <Link
            :href="route('stocks.edit', stock.id)"
            class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors"
            preserve-scroll
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
          </Link>
        </div>
      </div>

      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Stock Header -->
        <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-blue-600 to-blue-800">
          <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-white">
              {{ stock.name }} - {{ stock.location }}
            </h3>
            <span
              class="px-2 py-1 text-xs font-semibold rounded-full"
              :class="stock.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
            >
              {{ stock.status }}
            </span>
          </div>
        </div>

        <!-- Stock Details -->
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
          <dl class="sm:divide-y sm:divide-gray-200">
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Stock ID</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ stock.id }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Location</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ stock.location }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Status</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <span class="capitalize">{{ stock.status }}</span>
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Created At</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatDate(stock.created_at) }}</dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatDate(stock.updated_at) }}</dd>
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
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Search products..."
                class="pl-10 pr-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                @input="handleSearch"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden">
          <div class="overflow-x-auto">
            <Table>
              <template #header>
                <TableRow>
                  <TableHeaderCell @click="handleSort('name')" class="cursor-pointer select-none">
                    Product
                    <span v-if="sortField === 'name'">
                      {{ sortDirection === 'asc' ? '↑' : '↓' }}
                    </span>
                  </TableHeaderCell>
                  <TableHeaderCell @click="handleSort('category')" class="cursor-pointer select-none">
                    Category
                    <span v-if="sortField === 'category'">
                      {{ sortDirection === 'asc' ? '↑' : '↓' }}
                    </span>
                  </TableHeaderCell>
                  <TableHeaderCell @click="handleSort('price')" class="cursor-pointer select-none">
                    Price
                    <span v-if="sortField === 'price'">
                      {{ sortDirection === 'asc' ? '↑' : '↓' }}
                    </span>
                  </TableHeaderCell>
                  <TableHeaderCell @click="handleSort('quantity')" class="cursor-pointer select-none">
                    Quantity
                    <span v-if="sortField === 'quantity'">
                      {{ sortDirection === 'asc' ? '↑' : '↓' }}
                    </span>
                  </TableHeaderCell>
                  <TableHeaderCell @click="handleSort('total_value')" class="cursor-pointer select-none">
                    Total Value
                    <span v-if="sortField === 'total_value'">
                      {{ sortDirection === 'asc' ? '↑' : '↓' }}
                    </span>
                  </TableHeaderCell>
                </TableRow>
              </template>

              <template #body>
                <TableRow v-for="product in products.data" :key="product.id">
                  <TableDataCell>
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg
                          class="h-6 w-6 text-blue-600"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                          />
                        </svg>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                      </div>
                    </div>
                  </TableDataCell>
                  <TableDataCell>
                    <div
                      v-for="category in product.categories"
                      :key="category.id"
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mb-1"
                    >
                      {{ category.name || 'Uncategorized' }}
                    </div>
                  </TableDataCell>
                  <TableDataCell>{{ formatPrice(product.price) }}</TableDataCell>
                  <TableDataCell>{{ product.pivot.quantity }}</TableDataCell>
                  <TableDataCell>{{ formatPrice(product.price * product.pivot.quantity) }}</TableDataCell>
                </TableRow>

                <TableRow v-if="products.data.length === 0">
                  <TableDataCell colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    No products found in this stock location
                  </TableDataCell>
                </TableRow>

                <TableRow v-if="products.data.length > 0">
                  <TableDataCell colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                    Total Inventory Value:
                  </TableDataCell>
                  <TableDataCell class="px-6 py-3 text-sm font-bold text-gray-900">
                    {{ formatPrice(stock.totalValue) }}
                  </TableDataCell>
                </TableRow>
              </template>
            </Table>
          </div>

          <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700" v-if="products.meta.total > 0">
                Showing <span class="font-medium">{{ products.meta.from }}</span> to
                <span class="font-medium">{{ products.meta.to }}</span> of
                <span class="font-medium">{{ products.meta.total }}</span> results
              </div>
              <Pagination
                v-if="products.meta.links"
                :links="products.meta.links"
                @change="handlePageChange"
                preserve-scroll
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import Table from '@/Components/Table.vue'
import TableRow from '@/Components/TableRow.vue'
import TableHeaderCell from '@/Components/TableHeaderCell.vue'
import TableDataCell from '@/Components/TableDataCell.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { formatPrice } from '@/utils/format.js'
import { formatDate } from '@/utils/formatDate.js'

const props = defineProps({
  stock: {
    type: Object,
    required: true,
  },
  products: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

// Reactive search and sorting state initialized from props.filters
const searchQuery = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || 'name')
const sortDirection = ref(props.filters.direction || 'asc')

const handleSearch = debounce(() => {
  router.get(
    route('stocks.show', props.stock.id),
    {
      search: searchQuery.value,
      sort: sortField.value,
      direction: sortDirection.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  )
}, 300)

const handleSort = (field) => {
  if (sortField.value === field) {
    // toggle direction
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }

  router.get(
    route('stocks.show', props.stock.id),
    {
      search: searchQuery.value,
      sort: sortField.value,
      direction: sortDirection.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  )
}

const handlePageChange = (url) => {
  if (!url) return
  router.visit(url, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Watch searchQuery to trigger search after debounce
watch(searchQuery, () => {
  handleSearch()
})
</script>

<style scoped>
.cursor-pointer {
  user-select: none;
}
</style>
