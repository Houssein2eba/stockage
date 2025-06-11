<template>
  <AuthLayout>
    <Head title="Stocks" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Stocks d'Inventaire</h1>
          <p class="text-sm text-gray-500">Gérez votre inventaire de produits</p>
        </div>
        <div>
          <Link
            :href="route('stocks.create')"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Ajouter un Nouveau Stock
          </Link>
        </div>
      </div>

      <!-- Search and Filter -->
      <div class="mb-6 flex items-center justify-between">
        <div class="relative w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            type="text"
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            placeholder="Rechercher des stocks..."
            v-model="searchQuery"
          />
        </div>
        <div class="flex items-center space-x-4">

          <button @click="clearFilters" class="text-gray-500 hover:text-gray-700">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Stock Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Demo Stock Cards -->
        <div v-for="stock in props.stocks.data" :key="stock.id" class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                  <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">{{ stock.name }}</h3>
                  <p class="text-sm text-gray-500">{{ stock.location }}</p>
                </div>
              </div>
              <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="stock.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ stock.status }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-6">
            <div>
                <p class="text-sm text-gray-500">Types de Produits</p>
                <p  class="text-lg font-semibold">{{ stock.productsCount }}  </p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Nombre de Produits</p>
                <p  class="text-lg font-semibold">{{ stock.totalProducts }}  </p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Valeur Totale</p>
                <p class="text-lg font-semibold">{{ formatPrice(stock.totalValue) }}</p>
              </div>
              <!-- <div>
                <p class="text-sm text-gray-500">Capacity</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                  <div class="bg-blue-600 h-2 rounded-full" :style="{ width: stock.capacityPercentage + '%' }"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ stock.usedSpace }} / {{ stock.totalSpace }}</p>
              </div> -->
              <div>
                <p class="text-sm text-gray-500">Dernière Mise à Jour</p>
                <p class="text-sm">{{ formatDate( stock.updated_at) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
            <div class="flex space-x-2">

            </div>
            <Link
              :href="route('stocks.show', stock.id)"
              class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center"
            >
              Voir les Détails
              <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-8 flex items-center justify-between">
        <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700" v-if="stocks.meta.total > 0">
                            Affichage de <span class="font-medium">{{ stocks.meta.from }}</span> à
                            <span class="font-medium">{{ stocks.meta.to }}</span> sur
                            <span class="font-medium">{{ stocks.meta.total }}</span> résultats
                        </div>
                        <Pagination v-if="stocks.meta.links" :links="stocks.meta.links" @change="handlePageChange" />
                    </div>
                </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>

import Pagination from '@/Components/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { formatPrice } from '@/utils/format.js'
import { formatDate } from '@/utils/formatDate.js'
import { ref, watch } from 'vue'
import { debounce } from 'lodash';

const props = defineProps({
  stocks: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})


const searchQuery = ref(props.filters.search || '')

const handlePageChange = (url) => {
  if (!url) return;

  const urlObj = new URL(url);
  const pageParam = urlObj.searchParams.get('page');

  if (pageParam) {
    router.get(route('stocks.index'), {
      search: searchQuery.value,
      page: pageParam
    }, {
      preserveState: true,
      preserveScroll: true
    });
  }
};

watch(searchQuery, debounce((value) => {
  router.get(route('stocks.index'), {
    search: value,
    page: 1 // Reset to first page when searching
  }, {
    preserveState: true,
    preserveScroll: true
  });
}, 300))

const clearFilters = () => {
  searchQuery.value = ''
  router.get(route('stocks.index'), {}, {
    preserveState: true,
    preserveScroll: true
  })
}
</script>