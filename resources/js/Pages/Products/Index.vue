<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import VueMultiselect from 'vue-multiselect';
import { ref } from 'vue';

const form = useForm({
    name: "",
    description: "",
    price: "",
  quantity: "",
    min_quantity: "",
    category: null,
    image: null
});

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    products: {
        type: Array,
        default: () => []
    }
});

const toast = useToast();
const previewImage = ref(null);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        previewImage.value = URL.createObjectURL(file);
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MRU'
    }).format(price);
};
</script>

<template>
  <AuthLayout>
    <Head title="Product Management" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Header with stats -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Product Management</h1>
          <p class="text-gray-600 mt-1">Manage your product inventory</p>
        </div>
        <div class="flex items-center gap-3 bg-blue-50 px-4 py-2 rounded-lg">
          <span class="text-sm font-medium text-blue-800">
            Total Products: {{ products.length }}
          </span>
        </div>
      </div>

      <!-- Two-column layout -->
      <div class="flex flex-col lg:flex-row gap-6">
        <!-- Main content area (table) -->
        <div class="flex-1">
          <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
              <Table class="w-full min-w-[800px]">
                <thead class="bg-gray-50">
                  <TableRow>
                    <TableHeaderCell class="w-1/5">Product</TableHeaderCell>
                    <TableHeaderCell class="w-2/5">Description</TableHeaderCell>
                    <TableHeaderCell class="text-center">Image</TableHeaderCell>
                    <TableHeaderCell class="text-center">Price</TableHeaderCell>
                    <TableHeaderCell class="text-center">Stock</TableHeaderCell>
                    <TableHeaderCell class="text-center">Category</TableHeaderCell>
                    <TableHeaderCell class="text-right">Actions</TableHeaderCell>
                  </TableRow>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <TableRow v-for="product in products" :key="product.id" class="hover:bg-gray-50 transition-colors">
                    <TableDataCell>
                      <div class="font-medium text-gray-900">{{ product.name }}</div>
                    </TableDataCell>
                    <TableDataCell class="text-gray-600 text-sm">
                      <div class="line-clamp-2">{{ product.description || "No description" }}</div>
                    </TableDataCell>
                    <TableDataCell class="text-center">
                      <div class="flex justify-center">
                        <img 
                          :src="product.image || '/images/placeholder-product.png'" 
                          alt="Product" 
                          class="w-12 h-12 object-cover rounded-md"
                        />
                      </div>
                    </TableDataCell>
                    <TableDataCell class="text-center font-medium">
                      {{ formatPrice(product.price) }}
                    </TableDataCell>
                    <TableDataCell class="text-center">
                      <span 
                        :class="{
                          'bg-green-100 text-green-800': product.quantity > 10,
                          'bg-yellow-100 text-yellow-800': product.quantity > 0 && product.quantity <= 10,
                          'bg-red-100 text-red-800': product.quantity === 0
                        }"
                        class="px-2 py-1 rounded-full text-xs font-medium"
                      >
                        {{ product.quantity }} in stock
                      </span>
                    </TableDataCell>
                    <TableDataCell class="text-center text-sm">
                      <span v-if="product.categories.length > 0">
                        {{ product.categories.map(c => c.name).join(', ') }}
                      </span>
                      <span v-else class="text-gray-400">Uncategorized</span>
                    </TableDataCell>
                    <TableDataCell class="text-right">
                      <div class="flex justify-end space-x-3">
                        <Link 
                          :href="route('products.edit', product.id)"
                          class="text-blue-600 hover:text-blue-900 text-sm font-medium"
                        >
                          Edit
                        </Link>
                        <Link 
                          :href="route('products.destroy', product.id)"
                          method="delete"
                          as="button"
                          class="text-red-600 hover:text-red-900 text-sm font-medium"
                        >
                          Delete
                        </Link>
                      </div>
                    </TableDataCell>
                  </TableRow>
                  <TableRow v-if="products.length === 0">
                    <TableDataCell colspan="7" class="text-center py-8 text-gray-500">
                      <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="mt-2">No products found</p>
                        <p class="text-sm text-gray-400">Add your first product using the form</p>
                      </div>
                    </TableDataCell>
                  </TableRow>
                </tbody>
              </Table>
            </div>
          </div>
        </div>

        <!-- Sidebar form -->
        <div class="lg:w-96">
          <div class="bg-white shadow-sm rounded-lg border border-gray-200 sticky top-6">
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Add New Product</h2>
            </div>
            <form @submit.prevent="form.post(route('products.store'), {
              onSuccess: () => {
                toast.success('Product created successfully');
                form.reset();
                previewImage = null;
              },
              onError: () => {
                toast.error('Failed to create product');
              },
            })" class="p-6 space-y-4">
              <div>
                <InputLabel for="name" value="Product Name" />
                <TextInput
                  id="name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.name"
                  placeholder="Enter product name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <div>
                <InputLabel for="description" value="Description" />
                <textarea
                  id="description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  v-model="form.description"
                  placeholder="Enter product description"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.description" />
              </div>

              <div>
                <InputLabel for="image" value="Product Image" />
                <div class="mt-1 flex items-center gap-4">
                  <div v-if="previewImage" class="flex-shrink-0">
                    <img :src="previewImage" alt="Preview" class="h-16 w-16 object-cover rounded-md">
                  </div>
                  <label class="cursor-pointer">
                    <span class="sr-only">Choose product image</span>
                    <input
                      id="image"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleImageChange"
                    >
                    <span class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                      Choose File
                    </span>
                  </label>
                  <span v-if="!previewImage" class="text-sm text-gray-500">No file chosen</span>
                </div>
                <InputError class="mt-2" :message="form.errors.image" />
              </div>

              
                <div>
                  <InputLabel for="price" value="Price" />
                  <div class="mt-1 relative rounded-md shadow-sm">
                    
                    <TextInput
                      id="price"
                      type="number"
                      step="0.1"
                      min="0"
                      class="block w-full pl-7 pr-12"
                      v-model="form.price"
                      placeholder="0.0"
                    />
                  </div>
                  <InputError class="mt-2" :message="form.errors.price" />
                </div>

                <div>
                  <InputLabel for="quantity" value="Quantity" />
                  <TextInput
                    id="quantity"
                    type="number"
                    min="0"
                    class="mt-1 block w-full"
                    v-model="form.quantity"
                    placeholder="0"
                  />
                  <InputError class="mt-2" :message="form.errors.quantity" />
                </div>

                <div>
                  <InputLabel for="weight" value="Min Quantity" />
                  <TextInput
                    id="weight"
                    type="number"
                    step="1"
                    min="1"
                    class="mt-1 block w-full"
                    v-model="form.min_quantity"
                    placeholder="1"
                  />
                  <InputError class="mt-2" :message="form.errors.min_quantity" />
                </div>
              
              

              <div>
                <InputLabel for="category" value="Category" />
                <VueMultiselect 
                  v-model="form.category"
                  :options="categories"
                  :multiple="true"
                  :close-on-select="true"
                  placeholder="Select category"
                  label="name"
                  track-by="id"
                  class="mt-1"
                />
                <InputError class="mt-2" :message="form.errors.category" />
              </div>

              <div class="pt-2">
                <PrimaryButton
                  class="w-full justify-center"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  <span v-if="!form.processing">Add Product</span>
                  <span v-else>Processing...</span>
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>