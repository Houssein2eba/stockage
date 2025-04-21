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
    categories: Array,
    products:Array
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
//get image link
const getImageLink = (image) => {
    return image ? '/storage/' + image : '/images/placeholder-product.png';
};
// delete product logic

const showDeleteModal = ref(false);
const productToDelete = ref(null);

const confirmDelete = (productId) => {
  productToDelete.value = productId;
  showDeleteModal.value = true;
};

const deleteProduct = () => {
  form.delete(route('products.destroy', productToDelete.value), {
    onSuccess: () => {
      toast.success('Product deleted successfully');
      showDeleteModal.value = false;
      productToDelete.value = null;
    },
    onError: () => {
      toast.error('Failed to delete product');
      showDeleteModal.value = false;
      productToDelete.value = null;
    },
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  productToDelete.value = null;
};

</script>



<template>
  <AuthLayout>
    <Head title="Product Management" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header with stats -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Product Management</h1>
          <p class="text-gray-600 mt-1">Manage your product inventory</p>
        </div>
        <div class="flex items-center gap-3 bg-blue-50/80 px-4 py-2 rounded-lg border border-blue-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
          </svg>
          <span class="text-sm font-medium text-blue-800">
            Total Products: <span class="font-semibold">{{ props.products.data.length }}</span>
          </span>
        </div>
      </div>

      <!-- Search and Filter Card -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-xs mb-6">
        <div class="p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input
                  type="text"
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Search products..."
                />
              </div>
            </div>

            <!-- Order By Dropdown -->
            <div class="flex flex-row gap-4">
              <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="" selected disabled>Order by</option>
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
                <option value="price_asc">Price (Low to High)</option>
                <option value="price_desc">Price (High to Low)</option>
                <option value="quantity_asc">Stock (Low to High)</option>
                <option value="quantity_desc">Stock (High to Low)</option>
                <option value="created_at_desc">Newest First</option>
                <option value="created_at_asc">Oldest First</option>
              </select>

              <!-- Category Filter -->
              <select class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="" selected>All Categories</option>
                <option v-for="category in props.categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Two-column layout -->
      <div class="flex flex-col lg:flex-row gap-6">
        <!-- Main content area (table) -->
        <div class="flex-1 overflow-hidden">
          <div class="bg-white rounded-lg border border-gray-200 shadow-xs h-fit flex flex-col">
            <div class="overflow-x-auto flex-1">
              <div class="min-w-[1024px]">
                <Table class="w-full">
                  <thead class="bg-gray-50">
                    <TableRow class="border-b border-gray-200">
                      <TableHeaderCell class="w-[200px] py-3 pl-6">Product</TableHeaderCell>
                      <TableHeaderCell class="w-[300px]">Description</TableHeaderCell>
                      <TableHeaderCell class="w-[100px] text-center">Image</TableHeaderCell>
                      <TableHeaderCell class="w-[120px] text-center">Price</TableHeaderCell>
                      <TableHeaderCell class="w-[120px] text-center">Stock</TableHeaderCell>
                      <TableHeaderCell class="w-[200px] text-center">Category</TableHeaderCell>
                      <TableHeaderCell class="w-[150px] text-right pr-6">Actions</TableHeaderCell>
                    </TableRow>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <TableRow v-for="product in props.products.data" :key="product.id" class="hover:bg-gray-50/50 transition-colors">
                      <TableDataCell class="py-4 pl-6">
                        <div class="font-medium text-gray-900">{{ product.name }}</div>
                      </TableDataCell>
                      <TableDataCell class="text-gray-600 text-sm">
                        <div class="line-clamp-2">{{ product.description || "No description" }}</div>
                      </TableDataCell>
                      <TableDataCell class="text-center">
                        <div class="flex justify-center">
                          <img
                            :src="getImageLink(product.image) || 'https://i0.wp.com/georgiaautomation.com/wp-content/uploads/2018/09/image-placeholder.png?ssl=1'"
                            alt="Product"
                            class="w-10 h-10 object-cover rounded-md border border-gray-200"
                          />
                        </div>
                      </TableDataCell>
                      <TableDataCell class="text-center font-medium text-gray-900">
                        {{ formatPrice(product.price) }}
                      </TableDataCell>
                      <TableDataCell class="text-center">
                        <span
                          :class="{
                            'bg-green-100 text-green-800': product.quantity > 10,
                            'bg-yellow-100 text-yellow-800': product.quantity > product.min_quantity && product.quantity <=product.min_quantity+ 10,
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
                      </TableDataCell>
                      <TableDataCell class="text-center text-sm">
                        <div class="flex flex-wrap gap-1 justify-center">
                          <span
                            v-for="category in product.categories"
                            :key="category.id"
                            class="px-2.5 py-1 bg-blue-100/80 text-blue-800 rounded-full text-xs"
                          >
                            {{ category.name }}
                          </span>
                        </div>
                      </TableDataCell>
                      <TableDataCell class="text-right pr-6">
                        <div class="flex items-center justify-end gap-3">
                          <Link
                            :href="route('products.edit', product.id)"
                            class="text-blue-600 hover:text-blue-900 transition-colors flex items-center gap-1"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                          </Link>
                          <button
                            @click.prevent="confirmDelete(product.id)"
                            class="text-red-600 hover:text-red-900 transition-colors flex items-center gap-1"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                          </button>
                        </div>
                      </TableDataCell>
                    </TableRow>
                    <TableRow v-if=" props.products.data.length === 0">
                      <TableDataCell colspan="7" class="text-center py-12 text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                          <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                          </svg>
                          <p class="mt-3 text-gray-600 font-medium">No products found</p>
                          <p class="text-sm text-gray-400 mt-1">Add your first product using the form</p>
                        </div>
                      </TableDataCell>
                    </TableRow>
                  </tbody>
                </Table>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar form - unchanged -->
        <div class="lg:w-96">

          <div class="bg-white rounded-lg border border-gray-200 shadow-xs sticky top-6">
            <div class="p-5 border-b border-gray-200 bg-gray-50/50">
              <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add New Product
              </h2>
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
            })" class="p-5 space-y-5">
              <div>
                <InputLabel for="name" value="Product Name" class="mb-1.5" />
                <TextInput
                  id="name"
                  type="text"
                  class="w-full"
                  v-model="form.name"
                  placeholder="Enter product name"
                  :class="{ 'border-red-500': form.errors.name }"
                />
                <InputError class="mt-1.5" :message="form.errors.name" />
              </div>

              <div>
                <InputLabel for="description" value="Description" class="mb-1.5" />
                <textarea
                  id="description"
                  rows="3"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                  v-model="form.description"
                  placeholder="Enter product description"
                  :class="{ 'border-red-500': form.errors.description }"
                ></textarea>
                <InputError class="mt-1.5" :message="form.errors.description" />
              </div>

              <div>
                <InputLabel for="image" value="Product Image" class="mb-1.5" />
                <div class="flex items-center gap-4">
                  <div v-if="previewImage" class="flex-shrink-0">
                    <img :src="previewImage" alt="Preview" class="h-16 w-16 object-cover rounded-md border border-gray-200">
                  </div>
                  <label class="cursor-pointer flex-1">
                    <input
                      id="image"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleImageChange"
                      :class="{ 'border-red-500': form.errors.image }"
                    >
                    <div class="flex flex-col items-center justify-center px-6 py-8 border-2 border-dashed border-gray-300 rounded-md hover:border-blue-500 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span class="mt-2 text-sm text-gray-600">Click to upload</span>
                      <span class="text-xs text-gray-500">PNG, JPG up to 2MB</span>
                    </div>
                  </label>
                </div>
                <InputError class="mt-1.5" :message="form.errors.image" />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <InputLabel for="price" value="Price" class="mb-1.5" />
                  <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">MRU</span>
                    </div>
                    <TextInput
                      id="price"
                      type="number"
                      step="0.1"
                      min="0"
                      class="block w-full pl-14"
                      v-model="form.price"
                      placeholder="0.0"
                    />
                  </div>
                  <InputError class="mt-1.5" :message="form.errors.price" />
                </div>

                <div>
                  <InputLabel for="quantity" value="Quantity" class="mb-1.5" />
                  <TextInput
                    id="quantity"
                    type="number"
                    min="0"
                    class="w-full"
                    v-model="form.quantity"
                    placeholder="0"
                  />
                  <InputError class="mt-1.5" :message="form.errors.quantity" />
                </div>
              </div>

              <div>
                <InputLabel for="min_quantity" value="Min Quantity" class="mb-1.5" />
                <TextInput
                  id="min_quantity"
                  type="number"
                  step="1"
                  min="1"
                  class="w-full"
                  v-model="form.min_quantity"
                  placeholder="1"
                />
                <InputError class="mt-1.5" :message="form.errors.min_quantity" />
              </div>

              <div>
                <InputLabel for="category" value="Category" class="mb-1.5" />
                <VueMultiselect
                  v-model="form.category"
                  :options="categories"
                  :multiple="true"
                  :close-on-select="true"
                  placeholder="Select category"
                  label="name"
                  track-by="id"
                />
                <InputError class="mt-1.5" :message="form.errors.category" />
              </div>

              <div class="pt-2">
                <PrimaryButton
                  class="w-full justify-center py-2.5"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  <span v-if="!form.processing" class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Product
                  </span>
                  <span v-else class="flex items-center gap-2">
                    <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                  </span>
                </PrimaryButton>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <Transition name="fade">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Product</h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this product? This action cannot be undone.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <PrimaryButton

                @click="deleteProduct"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                :disabled="form.processing"
              >
                <span v-if="!form.processing">Delete</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Deleting...
                </span>
              </PrimaryButton>
              <button

                @click="showDeleteModal = false"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-700 text-base font-medium text-white  focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto sm:text-sm"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </AuthLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 150ms ease-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
