<template>
  <AuthLayout>
    <Head title="Create Product" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8 flex items-center justify-between">
        <div>
          <H1 >Create New Product</H1>
          <P>Add a new product to your inventory</P>
        </div>
        <div>
        <Link
          :href="route('products.index')"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
          </svg>
          Back to Products
        </Link>
    </div>
      </div>

      <div class="  rounded-lg shadow bg-white max-w-3xl mx-auto">
        <div class="px-4 py-5 sm:p-6 ">
          <form @submit.prevent="form.post(route('products.store'), {
            onSuccess: () => {
              toast.success('Product created successfully');
              router.visit(route('products.index'));
            },
            onError: () => {
              toast.error('Failed to create product');
            },
          })" class="space-y-6">
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
                class="w-full ring-1 ring-gray-300 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
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

            <div class="flex justify-end space-x-3">
              <Link
                :href="route('products.index')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
              >
                Cancel
              </Link>
              <PrimaryButton
                class="px-4 py-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                <span v-if="!form.processing" class="flex items-center gap-2">
                  Create Product
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
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'vue-toastification';
import VueMultiselect from 'vue-multiselect';

const props = defineProps({
  categories: Array
});

const toast = useToast();
const previewImage = ref(null);

const form = useForm({
  name: '',
  description: '',
  price: '',
  quantity: '',
  min_quantity: '',
  category: null,
  image: null
});

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.image = file;
    previewImage.value = URL.createObjectURL(file);
  }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
