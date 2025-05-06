<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

import {formatPrice} from "@/utils/format.js";
const props = defineProps({
    product: Object
});

const toast = useToast();



const getImageLink = (image) => {
    return image ? '/storage/' + image : '/images/placeholder-product.png';
};
</script>

<template>
    <AuthLayout>
        <Head :title="`${product.name} Details`" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <H1>Product Details</H1>
                    <P>View and manage product details.</P>
                </div>
                <div>
                    <Link
                        :href="route('products.index')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Products
                    </Link>
                </div>

            </div>
            <div class="mb-4">

            </div>

            <!-- Product details card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
                <div class="md:flex">
                    <!-- Product image -->
                    <div class="md:w-1/3 p-6 flex justify-center bg-gray-50">
                        <img
                            :src="getImageLink(product.image)"
                            :alt="product.name"
                            class="w-full h-auto max-h-96 object-contain"
                        />
                    </div>

                    <!-- Product info -->
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    <span
                                        v-for="category in product.categories"
                                        :key="category.id"
                                        class="px-2.5 py-1 bg-blue-100/80 text-blue-800 rounded-full text-xs"
                                    >
                                        {{ category.name }}
                                    </span>
                                </div>
                            </div>

                            <span
                                :class="{
                                    'bg-green-100 text-green-800': product.quantity > 10,
                                    'bg-yellow-100 text-yellow-800': product.quantity > product.min_quantity && product.quantity <= product.min_quantity + 10,
                                    'bg-red-100 text-red-800': product.quantity < product.min_quantity,
                                }"
                                class="px-2.5 py-1 rounded-full text-xs font-medium inline-flex items-center gap-1"
                            >
                                <svg v-if="product.quantity > 10" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else-if="product.quantity > 0" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ product.quantity }} in stock
                            </span>
                        </div>

                        <!-- Price -->
                        <div class="mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Price</h2>
                            <p class="mt-1 text-2xl font-bold text-blue-600">{{ formatPrice(product.price) }}</p>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <h2 class="text-lg font-medium text-gray-900">Description</h2>
                            <p class="mt-2 text-gray-600 whitespace-pre-line">{{ product.description || 'No description available' }}</p>
                        </div>

                        <!-- Additional details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">Created At</h2>
                                <p class="mt-1 text-gray-600">{{ new Date(product.created_at).toLocaleString() }}</p>
                            </div>
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">Updated At</h2>
                                <p class="mt-1 text-gray-600">{{ new Date(product.updated_at).toLocaleString() }}</p>
                            </div>
                            <div v-if="product.cost">
                                <h2 class="text-lg font-medium text-gray-900">Cost</h2>
                                <p class="mt-1 text-gray-600">{{ formatPrice(product.cost) }}</p>
                            </div>
                            <div >
                                <h2 class="text-lg font-medium text-gray-900">Benefit</h2>
                                <p class="mt-1 text-gray-600">{{ product.benefit===0 ? 'no benefit' : formatPrice(product.benefit) }}</p>
                            </div>
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">Sold Items</h2>
                                <p class="mt-1 text-gray-600">{{ product.sold || 0 }}</p>
                            </div>
                        </div>



                        <!-- Actions -->
                        <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                            <Link
                                :href="route('products.edit', product.id)"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Product
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
