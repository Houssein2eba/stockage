<template>
    <AuthLayout>
        <Head title="Invoice #{{ sale.reference }}" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Invoice Paper -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Company Header -->
                <div class="flex justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{props.setting.company_name}}</h1>
                        <p class="text-sm text-gray-600">Address: {{ props.setting.address }}</p>
                        <p class="text-sm text-gray-600">Téléphone: {{ props.setting.phone }}</p>
                        <p class="text-sm text-gray-600">Email: {{ props.setting.email }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-bold text-blue-600">#{{ sale.reference }}</div>
                        <p class="text-sm text-gray-600 mt-1">Date: {{ formatDate(sale.created_at) }}</p>
                        <p class="text-sm text-gray-600">Méthode de Paiement: {{ sale.payment?.name || 'Espèces' }}</p>
                        <p class="text-sm text-gray-600">Statut:
                            <span :class="{
                                'px-2 py-0.5 rounded-full text-xs font-medium': true,
                                'bg-green-100 text-green-800': sale.status === 'paid',
                                'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                                'bg-red-100 text-red-800': sale.status === 'cancelled'
                            }">
                                {{ sale.status }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Client Details -->
                <div v-if="sale.client" class="border-t border-b border-gray-200 py-6 mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations Client</h2>
                    <div class="grid grid-cols-2 gap-x-8">
                        <div>
                            <p class="font-medium text-gray-800">Nom: {{ sale.client.name }}</p>
                            <p class="text-gray-600">Téléphone: {{ sale.client.number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-8">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in sale.products" :key="product.id">
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900">{{ product.name }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 text-right">{{ formatPrice(product.price) }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900 text-right">{{ product.pivot.quantity }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900 text-right">{{ formatPrice(product.pivot.total_amount) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-sm font-medium text-gray-900 text-right">Total</td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-900 text-right">{{ formatPrice(totalAmount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>


                <!-- Actions -->
                <div class="mx-8 flex justify-end space-x-4 print:hidden">
                    <Link
                        :href="route('sales.index')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Retour
                    </Link>
                    <button

                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <a :href="route('sales.invoice', props.sale.id)" target="_blank">
  Télécharger la facture
</a>

                    </button>

                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>

import { formatPrice } from '@/utils/format.js'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
    sale: {
        type: Object,
        required: true
    },
    setting: {
        type: Object,
        required: true
    }
})

const totalAmount = props.sale.total_amount;



const printInvoice = () => {
    window.print()
}

const downloadPdf = () => {
    window.location.href = route('sales.invoice', props.sale.id)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}
</script>

<style>
@media print {
    /* Hide all page elements except invoice */
    body * {
        visibility: hidden;
    }

    /* Show only the invoice section */
    .max-w-4xl, .max-w-4xl * {
        visibility: visible;
    }

    /* Position the invoice at the top */
    .max-w-4xl {
        position: absolute;
        left: 0;
        top: 0;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    /* Hide all buttons and navigation */
    button, .print\:hidden {
        display: none !important;
    }

    /* Ensure background colors and borders print */
    * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Add page break inside prevention */
    table {
        page-break-inside: avoid;
    }

    /* Ensure white background */
    .bg-white {
        background-color: white !important;
    }
}

/* Regular styles */
.invoice-table {
    width: 100%;
    border-collapse: collapse;
}

.invoice-table th,
.invoice-table td {
    padding: 0.75rem 1rem;
}

.invoice-table th {
    background-color: #f9fafb;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}
</style>
