<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';
import Layout from '@/layouts/AuthLayout.vue';
import Table from '@/Components/Table.vue';
import TableRow from '@/Components/TableRow.vue';
import TableHeaderCell from '@/Components/TableHeaderCell.vue';
import TableDataCell from '@/Components/TableDataCell.vue';
import Pagination from '@/Components/Pagination.vue';
import { formatDate } from '@/utils/formatDate.js';

const props = defineProps({
    movements: Object,
    stock: Object,
    filters: {
        type: Object,
        default: () => ({
            search: '',
            date: '',
            sort: 'created_at',
            direction: 'desc'
        })
    }
});

// Reactive state
const sort = ref({
    field: props.filters.sort || 'created_at',
    direction: props.filters.direction || 'desc'
});
const searchQuery = ref(props.filters.search || '');
const dateFilter = ref(props.filters.date || '');
const typeFilter = ref(props.filters.type || '');

// Table headers configuration
const tableHeaders = computed(() => [
    { label: 'Produit', field: 'product_name', sortable: false },
    { label: 'Type', field: 'type', sortable: false },
    { label: 'Quantité', field: 'quantity', sortable: true },
    { label: 'Date', field: 'crated_at', sortable: true },


]);

// Get sort icon
const getSortIcon = (field) => {
    if (sort.value.field !== field) return '';
    return sort.value.direction === 'asc' ? '↑' : '↓';
};

// Handle sorting
const handleSort = (field) => {
    if (!tableHeaders.value.find(header => header.field === field)?.sortable) return;

    if (sort.value.field === field) {
        sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value.field = field;
        sort.value.direction = 'asc';
    }

    updateResults();
};

// Update results with current filters
const updateResults = debounce(() => {
    router.visit(route('stocks.show', props.stock.id), {
        data: {
            search: searchQuery.value,
            date: dateFilter.value,
            type: typeFilter.value,
            sort: sort.value.field,
            direction: sort.value.direction
        },
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 500);

// Watch for filter changes
watch([searchQuery, dateFilter,typeFilter], () => {
    updateResults();
});

// Clear all filters
const clearFilters = () => {
    searchQuery.value = '';
    dateFilter.value = '';
    typeFilter.value = '';
    sort.value = { field: 'created_at', direction: 'desc' };
};
</script>

<template>
    <Layout>
        <Head :title="`Stock Movements - ${stock.name}`" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Mouvements de Stock</h1>
                    <p class="text-sm text-gray-500">
                        Historique des mouvements pour :
                        <span class="font-medium">{{ stock.name }}</span>
                    </p>
                </div>
                <Link
                    :href="route('stocks.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-800 hover:bg-gray-200 focus:outline-none transition"
                >
                    Retour aux Stocks
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs mb-6">
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search Input -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher des Produits</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    id="search"
                                    type="text"
                                    v-model="searchQuery"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Rechercher produits..."
                                />
                            </div>
                        </div>

                        <!-- Date Filter -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Filtrer par Date</label>
                            <input
                                id="date"
                                type="date"
                                v-model="dateFilter"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            />
                        </div>
                        <!-- in out filter -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Filtrer par Type</label>
                            <select
                                id="type"
                                v-model="typeFilter"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            >
                                <option value="">Tous Types</option>
                                <option value="in">Entrée Stock</option>
                                <option value="out">Sortie Stock</option>
                            </select>
                        </div>

                        <!-- Effacer Filtres -->
                        <div class="flex items-end">
                            <button
                                @click="clearFilters"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-800 hover:bg-gray-200 focus:outline-none transition"
                            >
                                Effacer Filtres
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Movements Table -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
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
                                        <span v-if="header.sortable" class="ml-1">
                                            {{ getSortIcon(header.field) }}
                                        </span>
                                    </div>
                                </TableHeaderCell>
                            </TableRow>
                        </template>

                        <template #body>
                            <TableRow v-for="movement in movements.data" :key="movement.id">
                                <TableDataCell>
                                    {{ movement.product_name }}
                                </TableDataCell>
                                <TableDataCell>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': movement.type === 'in',
                                            'bg-red-100 text-red-800': movement.type === 'out'
                                        }"
                                    >
                                        {{ movement.type === 'in' ? 'Entrée Stock' : 'Sortie Stock' }}
                                    </span>
                                </TableDataCell>
                                <TableDataCell>
                                    {{ movement.quantity }}
                                </TableDataCell>
                                <TableDataCell>
                                    {{ movement.stock_date ? formatDate(movement.stock_date) : formatDate(movement.stock_date) }}
                                </TableDataCell>


                            </TableRow>
                            <TableRow v-if="movements.data.length === 0">
                                <TableDataCell colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucun mouvement trouvé avec les critères actuels
                                </TableDataCell>
                            </TableRow>
                        </template>
                    </Table>
                </div>

                <!-- Pagination -->
                <div v-if="movements.meta.total > 0" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Affichage des mouvements {{ movements.meta.from }} à {{ movements.meta.to }} sur un total de {{ movements.meta.total }}
                        </div>
                        <Pagination :links="movements.meta.links" />
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>