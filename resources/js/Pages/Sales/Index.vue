<template>
    <AuthLayout>
        <Head title="Sales Management" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header with stats -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Sales Management</h1>
                    <p class="text-gray-600 mt-1">View and manage your sales records</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('sales.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New Sale
                    </Link>
                </div>
            </div>

            <!-- Sales Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                            <p class="text-xl font-bold text-gray-900 truncate">{{ formatPrice(stats.totalRevenue) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Sales</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.totalSales }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Today's Sales</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.todaySales }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100">
                            <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending Payments</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.pendingPayments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <TextInput
                                v-model="filters.search"
                                type="search"
                                placeholder="Search sales..."
                                class="w-full"
                            />
                        </div>
                        <div class="flex items-center gap-4">
                            <select
                                v-model="filters.status"
                                class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <DatePicker
                                v-model="filters.date"
                                placeholder="Select date"
                                class="w-[200px]"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <Table class="min-w-full divide-y divide-gray-200">
                        <template #header>
                            <TableRow>
                                <TableHeaderCell class="whitespace-nowrap">Reference</TableHeaderCell>
                                <TableHeaderCell class="whitespace-nowrap">Client</TableHeaderCell>
                                <TableHeaderCell class="whitespace-nowrap">Items</TableHeaderCell>
                                <TableHeaderCell class="whitespace-nowrap">Total</TableHeaderCell>
                                <TableHeaderCell class="whitespace-nowrap">Status</TableHeaderCell>
                                <TableHeaderCell class="whitespace-nowrap">Date</TableHeaderCell>
                                <TableHeaderCell :colspan="3" class="text-right whitespace-nowrap">Actions</TableHeaderCell>
                            </TableRow>
                        </template>
                        <template #default>
                            <TableRow v-for="sale in sales.data" :key="sale.id" class="hover:bg-gray-50">
                                <TableDataCell class="max-w-[120px] truncate">
                                    <div class="font-medium text-gray-900">#{{ sale.reference }}</div>
                                </TableDataCell>
                                <TableDataCell class="max-w-[150px]">
                                    <div class="truncate">
                                        <div class="text-sm font-medium text-gray-900">{{ sale.client?.name || 'No Client' }}</div>
                                        <div class="text-xs text-gray-400">{{ sale.client?.number || '-' }}</div>
                                    </div>
                                </TableDataCell>
                                <TableDataCell>
                                    <div class="text-sm">{{ sale.items }}</div>
                                </TableDataCell>
                                <TableDataCell>
                                    <div class="font-medium whitespace-nowrap">{{ formatPrice(sale.total_amount) }}</div>
                                </TableDataCell>
                                <TableDataCell>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize whitespace-nowrap"
                                        :class="{
                                            'bg-green-100 text-green-800': sale.status === 'paid',
                                            'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                                            'bg-red-100 text-red-800': sale.status === 'cancelled'
                                        }"
                                    >
                                        {{ sale.status }}
                                    </span>
                                </TableDataCell>
                                <TableDataCell class="whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ formatDate(sale.created_at) }}
                                    </div>
                                </TableDataCell>
                                <TableDataCell class="whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <button
                                            v-if="sale.status !== 'paid'"
                                            @click="markAsPaid(sale)"
                                            class="text-green-600 hover:text-green-900 text-sm font-medium"
                                        >
                                            Mark as Paid
                                        </button>
                                        <Link
                                            :href="route('sales.show', sale.id)"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium"
                                        >
                                            View Details
                                        </Link>
                                        <button
                                            @click="confirmDelete(sale.id)"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </TableDataCell>
                            </TableRow>
                            <TableRow v-if="sales.length === 0">
                                <TableDataCell colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <p class="mt-4 text-lg font-medium text-gray-900">No sales found</p>
                                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new sale.</p>
                                    </div>
                                </TableDataCell>
                            </TableRow>
                        </template>
                    </Table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <Pagination :links="sales.links" />
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Delete Sale</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to delete this sale? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="closeDeleteModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                        :disabled="processing"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteSale"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700"
                        
                    >
                        <span >Delete Sale</span>
                        
                    </button>
                </div>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { format } from 'date-fns'
import AuthLayout from '@/layouts/AuthLayout.vue'
import Table from '@/Components/Table.vue'
import TableRow from '@/Components/TableRow.vue'
import TableHeaderCell from '@/Components/TableHeaderCell.vue'
import TableDataCell from '@/Components/TableDataCell.vue'
import TextInput from '@/Components/TextInput.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import DatePicker from 'primevue/datepicker'
import {router} from '@inertiajs/vue3'
const props = defineProps({
    sales: Object,
    stats: Object,
    filters: Object
})

const toast = useToast()
const showDeleteModal = ref(false)
const saleToDelete = ref(null)
const processing = ref(false)

const filters = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    date: props.filters?.date ? new Date(props.filters.date) : null
})

const stats = computed(() => ({
    totalRevenue: props.stats?.totalRevenue || 0,
    totalSales: props.stats?.totalSales || 0,
    todaySales: props.stats?.todaySales || 0,
    pendingPayments: props.stats?.pendingPayments || 0
}))

// Watch for filter changes
watch(filters.value, (newFilters) => {
    router.get(route('sales.index'), {
        ...newFilters,
        date: newFilters.date ? format(newFilters.date, 'yyyy-MM-dd') : null
    }, {
        preserveState: true,
        preserveScroll: true
    })
}, { deep: true })

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MRU'
    }).format(price || 0)
}

const formatDate = (date) => {
    return format(new Date(date), 'MMM dd, yyyy HH:mm')
}

const confirmDelete = (sale) => {
    saleToDelete.value = sale
    console.log(saleToDelete.value)
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    saleToDelete.value = null
    processing.value = false
}

const deleteSale = () => {
    router.delete(route('sales.destroy', saleToDelete.value), {
        onSuccess: () => {
            toast.success('Sale deleted successfully')
            showDeleteModal.value = false
            saleToDelete.value = null
        },
        onError: () => {
            toast.error('Failed to delete sale')
            showDeleteModal.value = false
            saleToDelete.value = null
        },
        preserveScroll: true
    })
}

const markAsPaid = (sale) => {
    router.put(route('sales.update', sale.id), {
        status: 'paid'
    }, {
        onSuccess: () => {
            toast.success('Sale marked as paid')
        },
        onError: () => {
            toast.error('Failed to update sale status')
        }
    })
}
</script>