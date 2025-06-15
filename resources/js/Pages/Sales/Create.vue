<template>
    <AuthLayout>
        <Head title="Create Sale" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Créer une vente</h1>
                    <p class="text-sm text-gray-500">Créer un nouvel enregistrement de vente</p>
                </div>
                <div>
                    <Link
                        :href="route('sales.index')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour aux ventes
                    </Link>
                </div>
            </div>

            <form @submit.prevent="submitForm">
                <div class="space-y-6 bg-white rounded-lg shadow p-6">
                    <!-- Client Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                        <VueMultiselect
                            v-model="form.client"
                            :options="clients"
                            :custom-label="client => `${client.name} - ${client.number}`"
                            placeholder="Sélectionner ou rechercher un client"
                            label="name"
                            track-by="id"
                            :searchable="true"
                            :class="{ 'border-red-500': form.errors.client }"
                        />
                        <InputError class="mt-1.5" :message="form.errors.client" />
                    </div>

                    <!-- Stock Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Emplacement du stock</label>
                        <div class="p-4 border rounded-lg">
                            <VueMultiselect
                                v-model="form.stock"
                                :options="stocks"
                                :custom-label="stock => `${stock.name} (${stock.location})`"
                                placeholder="Sélectionner un emplacement de stock"
                                label="name"
                                track-by="id"
                                @input="updateAvailableProducts"
                                :searchable="true"
                                :class="{ 'border-red-500': form.errors.stock }"
                            />
                            <InputError class="mt-1.5" :message="form.errors.stock" />
                        </div>
                    </div>

                    <!-- Products Selection and Display -->
                    <div class="space-y-4" v-if="form.stock">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Produits</h3>
                            <button
                                type="button"
                                @click="addProduct"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                :disabled="!form.stock || !hasAvailableProducts"
                            >
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Ajouter un produit
                            </button>
                        </div>

                        <!-- Products Cards -->
                        <div class="grid gap-4">
                            <div
                                v-for="(product, index) in form.products"
                                :key="index"
                                class="border border-gray-200 rounded-lg p-4 bg-gray-50"
                            >
                                <div class="flex items-start justify-between mb-4">
                                    <h4 class="text-sm font-medium text-gray-900">Produit {{ index + 1 }}</h4>
                                    <button
                                        type="button"
                                        @click="removeProduct(index)"
                                        class="text-red-500 hover:text-red-700 p-1"
                                        v-if="form.products.length > 1"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Product Selection -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Sélectionner le produit</label>
                                        <VueMultiselect
                                            v-model="product.product"
                                            :options="getAvailableProducts(form.stock, index)"
                                            :custom-label="prod => {
                                                const quantity = getAvailableQuantity(form.stock, prod);
                                                return `${prod.name} - ${formatPrice(prod.price)} (Stock: ${quantity})`;
                                            }"
                                            placeholder="Rechercher et sélectionner un produit"
                                            label="name"
                                            track-by="id"
                                            @input="() => onProductChange(index)"
                                            :searchable="true"
                                            :allow-empty="false"
                                            :class="{ 'border-red-500': form.errors[`products.${index}.product`] }"
                                        >
                                            <template #option="{ option }">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <div class="font-medium">{{ option.name }}</div>
                                                        <div class="text-sm text-gray-500">{{ formatPrice(option.price) }}</div>
                                                    </div>
                                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                                        Stock: {{ getAvailableQuantity(form.stock, option) }}
                                                    </span>
                                                </div>
                                            </template>
                                            <template #singleLabel="{ option }">
                                                <div class="flex justify-between items-center w-full">
                                                    <span>{{ option.name }}</span>
                                                    <span class="text-sm text-gray-500">{{ formatPrice(option.price) }}</span>
                                                </div>
                                            </template>
                                        </VueMultiselect>
                                        <InputError class="mt-1" :message="form.errors[`products.${index}.product`]" />
                                    </div>

                                    <!-- Quantity Input -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantité</label>
                                        <div class="relative">
                                            <TextInput
                                                type="number"
                                                min="1"
                                                :max="getAvailableQuantity(form.stock, product.product)"
                                                v-model.number="product.quantity"
                                                @input="calculateTotals"
                                                class="w-full"
                                                :class="{ 'border-red-500': form.errors[`products.${index}.quantity`] }"
                                                :disabled="!product.product"
                                            />
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 text-sm" v-if="product.product">
                                                    /{{ getAvailableQuantity(form.stock, product.product) }}
                                                </span>
                                            </div>
                                        </div>
                                        <InputError class="mt-1" :message="form.errors[`products.${index}.quantity`]" />
                                    </div>
                                </div>

                                <!-- Product Summary -->
                                <div v-if="product.product" class="mt-4 p-3 bg-white rounded border">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Prix unitaire:</span>
                                            <div class="font-medium">{{ formatPrice(product.product.price) }}</div>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Quantité:</span>
                                            <div class="font-medium">{{ product.quantity }}</div>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Stock restant:</span>
                                            <div class="font-medium text-green-600">{{ getAvailableQuantity(form.stock, product.product) - product.quantity }}</div>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Total:</span>
                                            <div class="font-bold text-lg">{{ formatPrice(product.total_amount || 0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No products message -->
                        <div v-if="!hasAvailableProducts" class="text-center py-4 text-gray-500">
                            Aucun produit disponible dans ce stock
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="border-t pt-6">
                        <div>
                            <label class="flex items-center space-x-3">
                                <input
                                    type="checkbox"
                                    v-model="form.paid"
                                    true-value="1"
                                    false-value="0"
                                    class="form-checkbox h-5 w-5 text-blue-600 rounded"
                                />
                                <span class="text-sm font-medium text-gray-700">Marquer comme payé</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">
                                Cochez cette case si le paiement a été effectué
                            </p>
                            <InputError class="mt-1.5" :message="form.errors.paid" />
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="border-t pt-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Résumé de la commande</h3>

                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-base">
                                    <span class="text-gray-600">Nombre d'articles:</span>
                                    <span class="font-medium">{{ getTotalItems() }}</span>
                                </div>

                                <div class="flex justify-between items-center text-lg font-bold border-t pt-2 mt-3">
                                    <span>Total à payer:</span>
                                    <span class="text-blue-600">{{ formatPrice(calculateTotal) }}</span>
                                </div>

                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Statut:</span>
                                    <span :class="form.paid == 1 ? 'text-green-600 font-medium' : 'text-orange-600 font-medium'">
                                        {{ form.paid == 1 ? 'Payé' : 'En attente de paiement' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3 border-t pt-6">
                        <Link
                            :href="route('sales.index')"
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Annuler
                        </Link>
                        <button
                            type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="form.processing || !canSubmit"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing || !canSubmit }"
                        >
                            <span v-if="form.processing">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enregistrement...
                            </span>
                            <span v-else>Enregistrer la vente</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/layouts/AuthLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const toast = useToast()

const props = defineProps({
    clients: {
        type: Array,
        default: () => []
    },
    stocks: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    client: null,
    stock: null,
    products: [{
        product: null,
        quantity: 1,
        unit_price: 0,
        total_amount: 0
    }],
    paid: 0,
    subtotal: 0,
    order_total_amount: 0
})

// Get available products for the selected stock
const getAvailableProducts = (stock, currentIndex = -1) => {
    if (!stock || !stock.products) return []

    // Get all selected product IDs except the one in the current row
    const selectedIds = form.products
        .map((p, i) => i !== currentIndex && p.product?.id)
        .filter(Boolean)

    // Return products not already selected and have quantity > 0
    return stock.products.filter(p => !selectedIds.includes(p.id) && p.quantity > 0)
}

// Get available quantity for a product in the selected stock
const getAvailableQuantity = (stock, product) => {
    if (!stock || !product || !stock.products) return 0
    const stockProduct = stock.products.find(p => p.id === product.id)
    return stockProduct?.quantity || 0
}

// Check if there are available products
const hasAvailableProducts = computed(() => {
    if (!form.stock) return false
    return getAvailableProducts(form.stock).length > 0
})

// Update available products when stock changes
const updateAvailableProducts = () => {
    form.products = [{
        product: null,
        quantity: 1,
        unit_price: 0,
        total_amount: 0
    }]
    calculateTotals()
}

// Handle product change
const onProductChange = (index) => {
    const product = form.products[index]
    if (product.product) {
        // Reset quantity to 1 when product changes
        product.quantity = 1
        // Set max quantity based on available stock
        const maxQuantity = getAvailableQuantity(form.stock, product.product)
        if (product.quantity > maxQuantity) {
            product.quantity = maxQuantity
        }
    }
    calculateTotals()
}

// Calculate totals for all products and order
const calculateTotals = () => {
    let subtotal = 0

    // Calculate each product's total and update form
    form.products.forEach((product, index) => {
        if (product.product && product.quantity) {
            const unitPrice = product.product.price
            const total = unitPrice * product.quantity

            form.products[index].unit_price = unitPrice
            form.products[index].total_amount = total
            subtotal += total
        } else {
            form.products[index].unit_price = 0
            form.products[index].total_amount = 0
        }
    })

    // Update form values
    form.subtotal = subtotal
    form.order_total_amount = subtotal
}

// Computed properties for display
const calculateTotal = computed(() => form.order_total_amount)

// Get total number of items
const getTotalItems = () => {
    return form.products.reduce((total, product) => {
        return total + (product.product && product.quantity ? product.quantity : 0)
    }, 0)
}

// Check if form can be submitted
const canSubmit = computed(() => {
    return form.client &&
           form.stock &&
           form.products.length > 0 &&
           form.products.some(p => p.product && p.quantity > 0) &&
           !form.processing
})

// Add product
const addProduct = () => {
    if (!hasAvailableProducts.value) return

    form.products.push({
        product: null,
        quantity: 1,
        unit_price: 0,
        total_amount: 0
    })
}

// Remove product
const removeProduct = (index) => {
    if (form.products.length > 1) {
        form.products.splice(index, 1)
        calculateTotals()
    }
}

// Format price helper
const formatPrice = (value) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'MRU',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value || 0)
}

const submitForm = () => {
    // Validate that we have products with valid selections
    const validProducts = form.products.filter(p => p.product && p.quantity > 0)

    if (validProducts.length === 0) {
        toast.error('Veuillez sélectionner au moins un produit')
        return
    }

    // Check stock availability
    for (const product of validProducts) {
        const availableQuantity = getAvailableQuantity(form.stock, product.product)
        if (product.quantity > availableQuantity) {
            toast.error(`Quantité insuffisante pour ${product.product.name}. Stock disponible: ${availableQuantity}`)
            return
        }
    }

    // Prepare the submission data
    const submissionData = {
        client_id: form.client?.id,
        stock_id: form.stock?.id,
        products: validProducts.map(product => ({
            product_id: product.product?.id,
            quantity: product.quantity,
            unit_price: product.unit_price,
            total_amount: product.total_amount
        })),
        subtotal: form.subtotal,
        order_total_amount: form.order_total_amount,
        paid: form.paid
    }

    form.post(route('sales.store'), {
        data: submissionData,
        onSuccess: () => {
            toast.success('Vente créée avec succès')
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                })
            })
        }
    })
}

// Initialize calculations
calculateTotals()
</script>

<style>
/* Custom styles for vue-multiselect */
.multiselect__option--highlight {
    background: #3b82f6 !important;
}

.multiselect__option--selected.multiselect__option--highlight {
    background: #1d4ed8 !important;
}

.multiselect__tag {
    background: #3b82f6 !important;
}

.multiselect__tag-icon:hover {
    background: #1d4ed8 !important;
}
</style>
