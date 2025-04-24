<template>
    <AuthLayout>
        <Head title="Create Sale" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <H1 >Create Sale</H1>
                    <P >Create a new sale record</P>
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
                        <Combobox v-model="form.client_id">
                            <div class="relative">
                                <ComboboxInput 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :displayValue="(id) => clients.find(client => client.id === id)?.name || ''"
                                    @change="searchClients"
                                    placeholder="Select or search client..." />
                                    <InputError class="mt-1.5" :message="form.errors.client_id" />

                                 <ComboboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ComboboxOption
                                        v-for="client in clients"
                                        :key="client.id"
                                        :value="client.id"
                                        class="relative cursor-default select-none py-2 pl-3 pr-9 hover:bg-blue-50"
                                        v-slot="{ active, selected }"
                                    >
                                        <div>
                                            <span class="block truncate font-medium">{{ client.name }}</span>
                                            <span class="block truncate text-sm text-gray-500">{{ client.number }}</span>
                                        </div>
                                    </ComboboxOption>
                                </ComboboxOptions>

                            </div>
                        </Combobox>
                    </div>

                    <!-- Products -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Products</label>
                        <div class="space-y-4">
                            <div v-for="(item, index) in form.items" :key="index" class="flex items-start gap-4 p-4 border rounded-lg">
                                <!-- Product Selection -->
                                 <div class="flex-1">
                                    <Combobox v-model="item.product_id">
                                        <div class="relative">
                                            <ComboboxInput 
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                :displayValue="(id) => products.find(p => p.id === id)?.name || ''"
                                                @change="searchProducts"
                                                placeholder="Select product..." />
                                            <ComboboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                <ComboboxOption
                                                    v-for="product in products"
                                                    :key="product.id"
                                                    :value="product.id"
                                                    class="relative cursor-default select-none py-2 pl-3 pr-9 hover:bg-blue-50"
                                                    v-slot="{ active, selected }"
                                                >
                                                    <div>
                                                        <span class="block truncate font-medium">{{ product.name }}</span>
                                                        <span class="block truncate text-sm text-gray-500">
                                                            Price: {{ formatPrice(product.price) }} | Stock: {{ product.quantity }}
                                                        </span>
                                                    </div>
                                                </ComboboxOption>
                                            </ComboboxOptions>
                                        </div>
                                    </Combobox>
                                    
                                </div>

                                <!-- Quantity -->
                                <div class="w-32">
                                    <TextInput
                                        v-model="item.quantity"
                                        type="number"
                                        min="1"
                                        placeholder="Quantity"
                                        class="w-full"
                                    />
                                </div>

                                <!-- Subtotal -->
                                <div class="w-32 pt-2">
                                    {{ formatPrice(calculateItemTotal(item)) }}
                                </div>

                                <!-- Remove Button -->
                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <button
                                type="button"
                                @click="addItem"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Product
                            </button>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select
                            v-model="form.payment_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Select payment method</option>
                            <option v-for="payment in payments" :key="payment.id" :value="payment.id">
                                {{ payment.name }}
                            </option>
                        </select>
                        <InputError class="mt-1.5" :message="form.errors.payment_id" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Add any notes about this sale..."
                        ></textarea>
                    </div>

                    <!-- Total -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center text-lg font-medium">
                            <span>Total Amount:</span>
                            <span>{{ formatPrice(calculateTotal()) }}</span>
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

import { Link, router, useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/layouts/AuthLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption } from '@headlessui/vue'
import InputError from '@/Components/InputError.vue'
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
    }
})
console.log(props.errors)
const form = useForm({
    client_id: null,
    items: [{ product_id: null, quantity: 1 }],
    payment_id: null,
    notes: ''
})

const addItem = () => {
    form.items.push({ product_id: null, quantity: 1 })
}

const removeItem = (index) => {
    form.items.splice(index, 1)
}

const calculateItemTotal = (item) => {
    if (!item.product_id || !item.quantity) return 0
    const product = props.products.find(p => p.id === item.product_id)
    return product ? product.price * item.quantity : 0
}

const calculateTotal = () => {
    return form.items.reduce((total, item) => total + calculateItemTotal(item), 0)
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MRU'
    }).format(price || 0)
}

const searchClients = (query) => {
    // TODO: Implement client search
}

const searchProducts = (query) => {
    // TODO: Implement product search
}

const submitForm = () => {
    if (form.items.some(item => !item.product_id)) {
        toast.error('Please select products for all items')
        return
    }

    form.post(route('sales.store'), {
        onSuccess: () => {
            toast.success('Sale created successfully');
            form.reset();
        },
        onError: () => {
            toast.error('Failed to create sale')
        }
    })
}
</script>