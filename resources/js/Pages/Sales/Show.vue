<template>
    <AuthLayout>
        <Head title="Facture #{{ sale.reference }}" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Invoice Paper -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Company Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">FACTURE</h1>
                    <p class="text-sm text-gray-600 mt-1">Votre Entreprise</p>
                    <p class="text-sm text-gray-600">123 Rue Principale, Ville</p>
                    <p class="text-sm text-gray-600">Tél: +222 00 00 00 00</p>
                </div>

                <!-- Invoice Details -->
                <div class="flex justify-between mb-8">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Client</h2>
                        <div class="mt-2 text-gray-600">
                            <p class="font-medium">{{ sale.client.name  }}</p>
                            <p class="font-medium">{{ sale.client.number  }}</p>
                            
                           
                        </div>
                    </div>
                    <div class="text-right">
                        <h2 class="text-lg font-semibold text-gray-800">Détails Facture</h2>
                        <div class="mt-2 text-gray-600">
                            <p>Facture #: {{ sale.reference }}</p>
                            <p>Date: {{ new Date(sale.created_at).toLocaleDateString() }}</p>
                            <p>Mode de paiement: {{ sale.payment || 'cash' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mt-8">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in sale.products" :key="product.id">
                                <td class="px-4 py-4 text-sm text-gray-900">{{ product.name }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900 text-right">{{ formatPrice(product.price) }}</td>
                                
                                   
                                
                                <td class="px-4 py-4 text-sm text-gray-900 text-right">{{ product.pivot.quantity}}</td>
                                <td class="px-4 py-4 text-sm text-gray-900 text-right">{{ formatPrice(product.pivot.total_amount) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            

                            <tr class="bg-gray-100">
                                <td colspan="2" class="px-4 py-4"></td>
                                <td class="px-4 py-4 text-base font-semibold text-gray-900 text-right">Total</td>
                                <td class="px-4 py-4 text-base font-semibold text-gray-900 text-right">{{ formatPrice(totalAmount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <p class="mb-2">Conditions de paiement:</p>
                        <p class="mb-4">Le paiement doit être effectué dans les 30 jours suivant la date de facturation.</p>
                        <p class="font-medium">Merci pour votre confiance!</p>
                    </div>
                </div>

                <!-- Print Button -->
                <div class="mt-8 text-right">
                    <button
                        @click="printInvoice"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimer Facture
                    </button>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    sale: {
        type: Object,
        required: true
    }
})

const totalAmount = props.sale.total_amount;

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'MRU'
    }).format(price || 0)
}

const printInvoice = () => {
    window.print()
}

</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .max-w-4xl, .max-w-4xl * {
        visibility: visible;
    }
    .max-w-4xl {
        position: absolute;
        left: 0;
        top: 0;
    }
    button {
        display: none !important;
    }
}
</style>