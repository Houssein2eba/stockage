<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import Pagination from "@/Components/Pagination.vue";
import { format } from 'date-fns';
import { formatPrice } from "@/utils/format.js";
const props = defineProps({
    sales: Object,
    stats: Object,
    filters: Object
});

const toast = useToast();
const showDeleteModal = ref(false);
const saleToDelete = ref(null);
const sort = ref({
    field: props.filters?.sort || 'created_at',
    direction: props.filters?.direction || 'desc'
});
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');
const dateFilter = ref(props.filters?.date || '');
const page = ref(props.sales?.meta?.current_page || 1);

// Computed property for table headers with sorting
const tableHeaders = computed(() => [
    { label: 'Référence', field: 'reference', sortable: true },
    { label: 'Client', field: 'client_id', sortable: true },
    { label: 'Total', field: 'total_amount', sortable: true },
    { label: 'Statut', field: 'status', sortable: true },
    { label: 'Date', field: 'created_at', sortable: true },
    { label: 'Actions', field: null, sortable: false }
]);

// Watch for filter changes with debounce
watch([search, statusFilter, dateFilter], debounce(() => {
    router.get(route('sales.index'), {
        search: search.value,
        status: statusFilter.value,
        date: dateFilter.value,
        sort: sort.value.field,
        direction: sort.value.direction,
        page: 1 // Reset to page 1 when filtering
    }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300));

const handleSort = (field) => {
    if (!field || !tableHeaders.value.find(header => header.field === field)?.sortable) return;

    if (sort.value.field === field) {
        sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value.field = field;
        sort.value.direction = 'asc';
    }

    router.get(route('sales.index'), {
        search: search.value,
        status: statusFilter.value,
        date: dateFilter.value,
        sort: sort.value.field,
        direction: sort.value.direction,
        page: page.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const handlePageChange = (url) => {
    if (!url) return;

    const urlObj = new URL(url);
    const pageParam = urlObj.searchParams.get('page');

    if (pageParam) {
        page.value = parseInt(pageParam);
        router.get(route('sales.index'), {
            search: search.value,
            status: statusFilter.value,
            date: dateFilter.value,
            sort: sort.value.field,
            direction: sort.value.direction,
            page: page.value
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
};

const getSortIcon = (field) => {
    if (!field || sort.value.field !== field) return 'none';
    return sort.value.direction === 'asc' ? 'asc' : 'desc';
};



const formatDate = (date) => {
    return format(new Date(date), 'PP');
};

const confirmDelete = (saleId) => {
    saleToDelete.value = saleId;
    showDeleteModal.value = true;
};

const deleteSale = () => {
    router.delete(route('sales.destroy', saleToDelete.value), {
        onSuccess: () => {
            toast.success('Vente supprimée avec succès');
            showDeleteModal.value = false;
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
            showDeleteModal.value = false;
        }
    });
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    saleToDelete.value = null;
};

const markAsPaid = (sale) => {
    router.put(route('sales.markAsPaid', sale.id), {
        onSuccess: () => {
            toast.success('Vente marquée comme payée');
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
        }
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Gestion des Ventes" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header with stats -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Ventes</h1>
                    <p class="text-gray-600 mt-1">Voir et gérer vos enregistrements de ventes</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('sales.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nouvelle Vente
                    </Link>
                </div>
            </div>

            <!-- Sales Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Revenu Total -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-500">Revenu Total</p>
                            <p class="text-xl font-bold text-gray-900 truncate">{{ formatPrice(stats.totalRevenue) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ventes Totales -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Ventes Totales</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.totalSales }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ventes du Jour -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Ventes du Jour</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.todaySales }}</p>
                        </div>
                    </div>
                </div>

                <!-- Paiements en Attente -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100">
                            <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Paiements en Attente</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.pendingPayments }}</p>
                        </div>
                    </div>
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
                                    placeholder="Rechercher des ventes..."
                                />
                            </div>
                        </div>

                        <!-- Statut Filter -->
                        <div class="w-full sm:w-48">
                            <select
                                v-model="statusFilter"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                            >
                                <option value="">Tous les statuts</option>
                                <option value="pending">En attente</option>
                                <option value="paid">Payé</option>
                                <option value="cancelled">Annulé</option>
                            </select>
                        </div>

                        <!-- Date Filter -->
                        <div class="w-full sm:w-48">
                            <input
                                type="date"
                                v-model="dateFilter"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs flex flex-col">
                <div class="overflow-x-auto flex-1">
                    <Table>
                        <template #header>
                            <TableRow>
                                <TableHeaderCell
                                    v-for="header in tableHeaders"
                                    :key="header.field || header.label"
                                    :class="[
                                        header.sortable ? 'cursor-pointer hover:bg-gray-100' : '',
                                        'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'
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
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3"
                                                :class="{'text-blue-600': getSortIcon(header.field) === 'desc'}"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </TableHeaderCell>
                            </TableRow>
                        </template>
                        <template #body>
                            <TableRow v-for="sale in sales.data" :key="sale.id" class="hover:bg-gray-50/50 transition-colors">
                                <TableDataCell class="py-4 pl-6">
                                    <Link
                                        :href="route('sales.show', sale.id)"
                                        class="text-blue-600 hover:text-blue-900 transition-colors font-medium"
                                    >
                                        #{{ sale.reference }}
                                    </Link>
                                </TableDataCell>
                                <TableDataCell>
                                    <div class="text-sm font-medium text-gray-900">{{ sale.client?.name || 'Aucun Client' }}</div>
                                    <div class="text-xs text-gray-500">{{ sale.client?.number || '-' }}</div>
                                </TableDataCell>
                                <TableDataCell class="font-medium text-gray-900">
                                    {{ formatPrice(sale.total_amount) }}
                                </TableDataCell>
                                <TableDataCell>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                        :class="{
                                            'bg-green-100 text-green-800': sale.status === 'paid',
                                            'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                                            'bg-red-100 text-red-800': sale.status === 'cancelled'
                                        }"
                                    >
                                        {{ 
                                            sale.status === 'paid' ? 'Payé' : 
                                            sale.status === 'pending' ? 'En attente' : 
                                            'Annulé'
                                        }}
                                    </span>
                                </TableDataCell>
                                <TableDataCell class="text-sm text-gray-500">
                                    {{ formatDate(sale.created_at) }}
                                </TableDataCell>
                                <TableDataCell class="text-right pr-6">
                                    <div class="flex items-center justify-end gap-3">
                                        <button
                                            v-if="sale.status !== 'paid'"
                                            @click="markAsPaid(sale)"
                                            class="text-green-600 hover:text-green-900 transition-colors flex items-center gap-1 text-sm"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Marquer Payé
                                        </button>
                                        <Link
                                            :href="route('sales.show', sale.id)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors flex items-center gap-1 text-sm"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Voir
                                        </Link>
                                        <button
                                            v-if="sale.status !== 'cancelled'"
                                            @click="confirmDelete(sale.id)"
                                            class="text-red-600 hover:text-red-900 transition-colors flex items-center gap-1 text-sm"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Cancel
                                        </button>
                                    </div>
                                </TableDataCell>
                            </TableRow>
                            <TableRow v-if="sales.data.length === 0">
                                <TableDataCell colspan="6" class="text-center py-12 text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <p class="mt-2 text-sm">No sales found</p>
                                        <p class="text-sm">Get started by creating a new sale</p>
                                    </div>
                                </TableDataCell>
                            </TableRow>
                        </template>
                    </Table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700" v-if="sales.meta.total > 0">
            Affichage de <span class="font-medium">{{ sales.meta.from }}</span> à
            <span class="font-medium">{{ sales.meta.to }}</span> sur
            <span class="font-medium">{{ sales.meta.total }}</span> résultats
        </div>
        <Pagination v-if="sales.meta.links" :links="sales.meta.links" @change="handlePageChange" />
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
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Sale</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Are you sure you want to cancel this sale? This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <PrimaryButton
                                @click="deleteSale"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                confirm
                            </PrimaryButton>
                            <button
                                @click="closeDeleteModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
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