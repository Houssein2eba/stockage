<template>
    <AuthLayout>
        <Head title="Create Sale" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Sale</h1>
                    <p class="text-sm text-gray-500">Create a new sale record</p>
                </div>
                <div>
                    <Link
                        :href="route('sales.index')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Sales
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
                            placeholder="Select or search client"
                            label="name"
                            track-by="id"
                            :class="{ 'border-red-500': form.errors.client }"
                        />
                        <InputError class="mt-1.5" :message="form.errors.client" />
                    </div>

                    <!-- Stock Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Location</label>
                        <div class="p-4 border rounded-lg">
                            <VueMultiselect
                                v-model="form.stock"
                                :options="stocks"
                                :custom-label="stock => `${stock.name} (${stock.location})`"
                                placeholder="Select stock location"
                                label="name"
                                track-by="id"
                                @input="updateAvailableProducts"
                                :class="{ 'border-red-500': form.errors.stock }"
                            />
                            <InputError class="mt-1.5" :message="form.errors.stock" />
                        </div>
                    </div>

                    <!-- Products in Selected Stock -->
                    <div class="space-y-3" v-if="form.stock">
                        <div v-for="(product, index) in form.products" :key="index" class="flex items-start gap-4 p-3 bg-gray-50 rounded">
                            <!-- Product Selection -->
                            <div class="flex-1">
                                <VueMultiselect
                                    v-model="product.product"
                                    :options="getAvailableProducts(form.stock)"
                                    :custom-label="prod => {
                                        const quantity = getAvailableQuantity(form.stock, prod);
                                        return `${prod.name} - ${formatPrice(prod.price)} (Available: ${quantity})`;
                                    }"
                                    placeholder="Select product"
                                    label="name"
                                    track-by="id"
                                    :class="{ 'border-red-500': form.errors[`products.${index}.product`] }"
                                />
                                <InputError class="mt-1.5" :message="form.errors[`products.${index}.product`]" />
                            </div>

                            <!-- Quantity -->
                            <div class="w-24">
                                <TextInput
                                    type="number"
                                    min="1"
                                    :max="getAvailableQuantity(form.stock, product.product)"
                                    v-model.number="product.quantity"
                                    class="w-full"
                                    placeholder="Qty"
                                    :class="{ 'border-red-500': form.errors[`products.${index}.quantity`] }"
                                />
                                <InputError class="mt-1.5" :message="form.errors[`products.${index}.quantity`]" />
                            </div>

                            <!-- Total -->
                            <div class="w-32 flex items-center justify-end">
                                <span class="font-medium">{{ formatPrice(calculateProductTotal(product)) }}</span>
                            </div>

                            <!-- Remove button -->
                            <button
                                type="button"
                                @click="removeProduct(index)"
                                class="text-red-500 hover:text-red-700"
                                v-if="form.products.length > 1"
                            >
                                Remove
                            </button>
                        </div>

                        <button
                            type="button"
                            @click="addProduct"
                            class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200"
                            :disabled="!form.stock"
                        >
                            + Add Product
                        </button>
                    </div>

                    <!-- Paid Checkbox -->
                    <div class="w-24">
                        <label for="paid-checkbox" class="block text-sm font-medium text-gray-700 mb-2">Paid</label>
                        <input
                            id="paid-checkbox"
                            type="checkbox"
                            v-model="form.paid"
                            true-value="1"
                            false-value="0"
                            class="form-checkbox ring-2 h-5 w-5 text-blue-600 transition duration-150 ease-in-out"
                        />
                        <InputError class="mt-1.5" :message="form.errors.paid" />
                    </div>

                    <!-- Total -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center text-lg font-medium">
                            <span>Total Amount:</span>
                            <span>{{ formatPrice(calculateTotal) }}</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <Link
                            :href="route('sales.index')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700"
                            :disabled="form.processing"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Sale</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>

<script setup>
import { formatPrice } from '@/utils/format.js'
import { computed } from 'vue'
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
    },
    products: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    client: null,
    stock: null,
    products: [{
        product: null,
        quantity: 1
    }],
    paid: 0
})

// Get available products for the selected stock
const getAvailableProducts = (stock, currentIndex = -1) => {
    if (!stock || !stock.products) return []

    // Get all selected product IDs except the one in the current row
    const selectedIds = form.products
        .map((p, i) => i !== currentIndex && p.product?.id)
        .filter(Boolean)

    // Return products not already selected
    return stock.products.filter(p => !selectedIds.includes(p.id))
}


// Get available quantity for a product in the selected stock
const getAvailableQuantity = (stock, product) => {
    if (!stock || !product || !stock.products) return 0

    const stockProduct = stock.products.find(p => p.id === product.id)
    return stockProduct?.quantity || 0
}

// Update available products when stock changes
const updateAvailableProducts = () => {
    form.products = [{
        product: null,
        quantity: 1
    }]
}

// Calculate total for a single product
const calculateProductTotal = (product) => {
    if (!product.product || !product.quantity) return 0
    return product.product.price * product.quantity
}

// Calculate total for all products
const calculateTotal = computed(() => {
    return form.products.reduce((total, product) => {
        return total + calculateProductTotal(product)
    }, 0)
})

// Add product
const addProduct = () => {
    form.products.push({
        product: null,
        quantity: 1
    })
}

// Remove product
const removeProduct = (index) => {
    if (form.products.length > 1) {
        form.products.splice(index, 1)
    }
}

const submitForm = () => {
    const submissionData = {
        client_id: form.client?.id,
        stock_id: form.stock?.id,
        products: form.products.map(product => ({
            product_id: product.product?.id,
            quantity: product.quantity
        })),
        paid: form.paid
    }

    form.post(route('sales.store'), {
        data: submissionData,
        onSuccess: () => {
            toast.success('Sale created successfully')
            form.reset()
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
</script>
