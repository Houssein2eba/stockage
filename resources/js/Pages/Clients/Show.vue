<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { useToast } from "vue-toastification";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import Pagination from "@/Components/Pagination.vue";
dayjs.extend(relativeTime);

const props = defineProps({
    client: Object,
    orders: Array,
});

const toast = useToast();

const amountFormat = (amount) => {
    return Number(amount).toFixed(2);
};

const formatDate = (date) => {
    return dayjs(date).fromNow(); // Example: "2 hours ago"
};
const exportExcel=() => {
  window.location.href = route('clients.exportclient', props.client.id);
}
</script>

<template>
    <AuthLayout>
        <Head title="Client Details" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Back Button -->
            <div>
                <Link
                    :href="route('clients.index')"
                    class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-md"
                >
                    ← Back to Clients
                </Link>
            </div>

            <!-- Client Details -->
            <div class="bg-white p-6 rounded-lg shadow-md space-y-4">
             <div class="flex justify-between">

                <h1 class="text-2xl font-bold text-gray-900">{{ props.client.name }}</h1>
                <div class="text-gray-700">
                    <p><strong>Client Number:</strong> {{ props.client.number }}</p>
                    <p><strong>Joined:</strong> {{ formatDate(props.client.created_at) }}</p>
                </div>
                <div>
                    <button
            @click="exportExcel"
            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export to Excel
          </button>
                </div>
            </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Total Orders</h2>
                    <p class="text-2xl font-bold text-blue-600">{{ props.client.orders_count}}</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Pending Depts</h2>
                    <p class="text-2xl font-bold text-red-500">{{ amountFormat(props.client.depts_amount) }} MRU</p>
                </div>
            </div>

            <!-- Orders List -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Orders</h2>

                <div v-if="props.orders.data" class="space-y-4">
                    <div
                        v-for="order in props.orders.data"
                        :key="order.id"
                        class="border p-4 rounded-lg flex justify-between items-center"
                    >
                        <div>
                            <p class="font-semibold text-gray-800">#{{ order.reference }}</p>
                            <p class="text-gray-500 text-sm">Total: {{ amountFormat(order.total_amount) }} MRU</p>
                        </div>
                        <div value="">
                            <p class="text-gray-500 text-sm">Created: {{ formatDate(order.created_at) }}</p>
                            <p class="text-gray-500 text-sm">Paid at: {{ formatDate(order.updated_at) }}</p>
                        </div>
                        <div>
                            <span >{{ order.payment?.name || 'N/A' }}</span>
                        </div>
                        <div>
                            <span
                                :class="{
                                    'bg-green-100 text-green-700': order.status === 'paid',
                                    'bg-yellow-100 text-yellow-700': order.status === 'pending'
                                }"
                                class="px-2 py-1 rounded-full text-xs font-semibold"
                            >
                                {{ order.status }}
                            </span>
                        </div>
                    </div>
                    <div class="justify-between items-center flex mt-4">
                        <div class="text-gray-500 text-sm">
                            Showing {{ props.orders.meta.from }} to {{ props.orders.meta.to }} of {{ props.orders.meta.total }} results
                        </div>

                    <div class="mt-4">
                        <Pagination :links="props.orders.meta.links" />
                    </div>
                </div>
                </div>

                <p v-else class="text-gray-500">No orders found.</p>
            </div>
        </div>
    </AuthLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 150ms ease-out;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
