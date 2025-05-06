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
                            @search-change="clientSearch"
                            :class="{ 'border-red-500': form.errors.client }"
                        />
                        <InputError class="mt-1.5" :message="form.errors.client" />
                    </div>

                    <!-- Products -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Products</label>
                        <div class="space-y-4">
                            <div v-for="(item, index) in form.items" :key="index" class="flex items-start gap-4 p-4 border rounded-lg">
                                <!-- Product Selection -->
                                <div class="flex-1">
                                    <VueMultiselect
                                        v-model="item.product"
                                        :options="products"
                                        :custom-label="product => `${product.name} - ${formatPrice(product.price)} (Stock: ${product.quantity})`"
                                        placeholder="Select product"
                                        label="name"
                                        track-by="id"
                                        @search-change="productSearch"
                                        :class="{ 'border-red-500': form.errors[`items.${index}.product`] }"
                                    />
                                    <InputError class="mt-1.5" :message="form.errors[`items.${index}.product`]" />
                                </div>

                                <!-- Quantity -->
                                <div class="w-24">
                                    <TextInput
                                        type="number"
                                        min="1"
                                        v-model.number="item.quantity"
                                        class="w-full"
                                        placeholder="Qty"
                                        :class="{ 'border-red-500': form.errors[`items.${index}.quantity`] }"
                                    />
                                    <InputError class="mt-1.5" :message="form.errors[`items.${index}.quantity`]" />
                                </div>

                                <!-- Total -->
                                <div class="w-32 flex items-center justify-end">
                                    <span class="font-medium">{{ formatPrice(calculateItemTotal(item)) }}</span>
                                </div>

                                <!-- Remove button -->
                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="text-red-500 hover:text-red-700"
                                    v-if="form.items.length > 1"
                                >
                                    Remove
                                </button>
                            </div>

                            <button
                                type="button"
                                @click="addItem"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Add Product
                            </button>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>

                        <VueMultiselect
                            v-model="form.payment"
                            :options="payments"
                            :custom-label="payment => payment.name"
                            placeholder="Select payment method"
                            label="name"
                            track-by="id"
                            :class="{ 'ring-2 ring-red-500': form.errors['payment.id'] }"
                        />

                        <InputError class="mt-1.5" :message="form.errors['payment.id']" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Add any notes about this sale..."
                            :class="{ 'border-red-500': form.errors.notes }"
                        ></textarea>
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
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/layouts/AuthLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import SelectPayment from '@/Components/SelectPayment.vue'
const toast = useToast()

const props = defineProps({
    clients: {
        type: Array,
        default: () => []
    },
    products: {
        type: Array,
        default: () => []
    },
    payments: {
        type: Array,
        default: () => []
    },
})

const form = useForm({
    client: null,
    items: [{ product: null, quantity: 0 }],
    payment: null,
    notes: ''
})

const addItem = () => {
    form.items.push({ product: null, quantity: 1 })
}

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1)
    }
}

const calculateItemTotal = (item) => {

    if (!item.product || !item.quantity) return 0
    const productId = item.product?.id || item.product;

    const product = props.products.find(p => String(p.id) === String(productId));

    if (!product) {
        console.error('Product not found for ID:', item.product)
        return 0
    }

    // Ensure quantity is treated as a number
    const quantity = Number(item.quantity) || 0
    const price = Number(product.price) || 0

    return price * quantity
}

const calculateTotal = computed(() => {
    return form.items.reduce((total, item) => total + calculateItemTotal(item), 0)
})

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MRU'
    }).format(price || 0)
}



const submitForm = () => {
    // Validate required fields


    form.post(route('sales.store'), {
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
