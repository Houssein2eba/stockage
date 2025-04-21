<template>
    <AuthLayout>
        <Head title="Edit Sale" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Edit Sale #{{ sale.reference }}</h1>
                <p class="mt-1 text-sm text-gray-600">Update sale details and items</p>
            </div>

            <div class="bg-white rounded-lg shadow divide-y divide-gray-200">
                <!-- Sale Info Section -->
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Sale Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                            <VueMultiselect
                                v-model="form.client"
                                :options="clients"
                                :searchable="true"
                                :allow-empty="true"
                                placeholder="Select client"
                                label="name"
                                track-by="id"
                            />
                            <InputError class="mt-1" :message="form.errors.client_id" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                            <VueMultiselect
                                v-model="form.payment"
                                :options="payments"
                                :searchable="true"
                                :allow-empty="true"
                                placeholder="Select payment method"
                                label="name"
                                track-by="id"
                            />
                            <InputError class="mt-1" :message="form.errors.payment_id" />
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Sale Items</h2>
                        <button
                            type="button"
                            @click="addItem"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Item
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-4">
                        <div v-for="(item, index) in form.items" :key="index" class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                                    <VueMultiselect
                                        v-model="item.product"
                                        :options="products"
                                        :searchable="true"
                                        placeholder="Select product"
                                        label="name"
                                        track-by="id"
                                        @input="updateItemTotal(index)"
                                    >
                                        <template #option="{ option }">
                                            <div class="flex items-center">
                                                <span>{{ option.name }}</span>
                                                <span class="ml-2 text-sm text-gray-500">
                                                    ({{ formatPrice(option.price) }} × {{ option.quantity }} in stock)
                                                </span>
                                            </div>
                                        </template>
                                    </VueMultiselect>
                                    <InputError class="mt-1" :message="form.errors[`items.${index}.product_id`]" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                    <TextInput
                                        type="number"
                                        v-model="item.quantity"
                                        min="1"
                                        :max="item.product?.quantity || 999999"
                                        class="w-full"
                                        @input="updateItemTotal(index)"
                                    />
                                    <InputError class="mt-1" :message="form.errors[`items.${index}.quantity`]" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ formatPrice(item.total) }}
                                    </div>
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="removeItem(index)"
                                class="p-2 text-red-600 hover:text-red-800"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div v-if="form.items.length === 0" class="text-center py-12">
                        <p class="text-gray-500">No items added yet. Click "Add Item" to start.</p>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div class="text-gray-700">
                            Total Items: <span class="font-medium">{{ form.items.length }}</span>
                        </div>
                        <div class="text-xl font-bold text-gray-900">
                            Total Amount: {{ formatPrice(totalAmount) }}
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="p-6 bg-gray-50 flex items-center justify-end gap-4">
                    <Link
                        :href="route('sales.index')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Cancel
                    </Link>
                    <button
                        type="button"
                        @click="updateSale"
                        :disabled="form.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50"
                    >
                        <span v-if="!form.processing">Update Sale</span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            Updating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'
import VueMultiselect from 'vue-multiselect'

const props = defineProps({
    sale: Object,
    clients: Array,
    products: Array,
    payments: Array
})

const toast = useToast()

const form = useForm({
    client: props.sale.client || null,
    payment: props.sale.payment || null,
    items: props.sale.products.map(product => ({
        product: {
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: product.quantity
        },
        quantity: product.pivot.quantity,
        total: product.pivot.total_amount
    }))
})

const totalAmount = computed(() => {
    return form.items.reduce((sum, item) => sum + (item.total || 0), 0)
})

const updateItemTotal = (index) => {
    const item = form.items[index]
    if (item.product && item.quantity) {
        item.total = item.product.price * item.quantity
    } else {
        item.total = 0
    }
}

const addItem = () => {
    form.items.push({
        product: null,
        quantity: 1,
        total: 0
    })
}

const removeItem = (index) => {
    form.items.splice(index, 1)
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MRU'
    }).format(price || 0)
}

const updateSale = () => {
    form.put(route('sales.update', props.sale.id), {
        onSuccess: () => {
            toast.success('Sale updated successfully')
        },
        onError: () => {
            toast.error('Failed to update sale')
        }
    })
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>