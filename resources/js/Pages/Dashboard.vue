<template>
  <AuthLayout>
    <Head title="Stocks" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Inventory Stocks</h1>
          <p class="text-sm text-gray-500">Manage your product inventory</p>
        </div>
        <div>
          <Link
            :href="route('stock.index')"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Stock
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
            placeholder="Search stocks..."
          />
        </div>
        <div class="flex items-center space-x-4">
          <select class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option>All Categories</option>
            <option>Electronics</option>
            <option>Clothing</option>
            <option>Food</option>
          </select>
          <button class="text-gray-500 hover:text-gray-700">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Stock Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Demo Stock Cards -->
        <div v-for="stock in demoStocks" :key="stock.id" class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
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
                <p class="text-sm text-gray-500">Products</p>
                <p class="text-lg font-semibold">{{ stock.productCount }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Total Value</p>
                <p class="text-lg font-semibold">{{ formatPrice(stock.totalValue) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Capacity</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                  <div class="bg-blue-600 h-2 rounded-full" :style="{ width: stock.capacityPercentage + '%' }"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ stock.usedSpace }} / {{ stock.totalSpace }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Last Updated</p>
                <p class="text-sm">{{ stock.lastUpdated }}</p>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
            <div class="flex space-x-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getCategoryColor(stock.category)">
                {{ stock.category }}
              </span>
            </div>
            <Link
              :href="route('stock.index')"
              class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center"
            >
              View Details
              <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-8 flex items-center justify-between">
        <div class="text-sm text-gray-500">
          Showing <span class="font-medium">1</span> to <span class="font-medium">6</span> of <span class="font-medium">24</span> stocks
        </div>
        <div class="flex space-x-2">
          <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Previous
          </button>
          <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
            1
          </button>
          <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            2
          </button>
          <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            3
          </button>
          <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Next
          </button>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { formatPrice } from '@/utils/format.js'

const demoStocks = [
  {
    id: 1,
    name: 'Main Warehouse',
    location: 'Building A, Floor 2',
    status: 'Active',
    productCount: 142,
    totalValue: 28450.75,
    capacityPercentage: 75,
    usedSpace: '750 sqm',
    totalSpace: '1000 sqm',
    lastUpdated: '2 hours ago',
    category: 'General'
  },
  {
    id: 2,
    name: 'Electronics Storage',
    location: 'Building B, Floor 1',
    status: 'Active',
    productCount: 87,
    totalValue: 58720.30,
    capacityPercentage: 62,
    usedSpace: '310 sqm',
    totalSpace: '500 sqm',
    lastUpdated: '1 day ago',
    category: 'Electronics'
  },
  {
    id: 3,
    name: 'Cold Storage',
    location: 'Building C',
    status: 'Maintenance',
    productCount: 35,
    totalValue: 12450.90,
    capacityPercentage: 45,
    usedSpace: '225 sqm',
    totalSpace: '500 sqm',
    lastUpdated: '3 days ago',
    category: 'Food'
  },
  {
    id: 4,
    name: 'Clothing Warehouse',
    location: 'Building A, Floor 1',
    status: 'Active',
    productCount: 210,
    totalValue: 35680.20,
    capacityPercentage: 82,
    usedSpace: '410 sqm',
    totalSpace: '500 sqm',
    lastUpdated: '5 hours ago',
    category: 'Clothing'
  },
  {
    id: 5,
    name: 'Raw Materials',
    location: 'Building D',
    status: 'Active',
    productCount: 68,
    totalValue: 9870.50,
    capacityPercentage: 34,
    usedSpace: '170 sqm',
    totalSpace: '500 sqm',
    lastUpdated: 'Yesterday',
    category: 'Materials'
  },
  {
    id: 6,
    name: 'Fragile Items',
    location: 'Building A, Floor 3',
    status: 'Active',
    productCount: 53,
    totalValue: 42360.75,
    capacityPercentage: 53,
    usedSpace: '265 sqm',
    totalSpace: '500 sqm',
    lastUpdated: '1 hour ago',
    category: 'Glassware'
  }
]

const getCategoryColor = (category) => {
  const colors = {
    'General': 'bg-gray-100 text-gray-800',
    'Electronics': 'bg-blue-100 text-blue-800',
    'Food': 'bg-green-100 text-green-800',
    'Clothing': 'bg-purple-100 text-purple-800',
    'Materials': 'bg-yellow-100 text-yellow-800',
    'Glassware': 'bg-red-100 text-red-800'
  }
  return colors[category] || 'bg-gray-100 text-gray-800'
}
</script>
