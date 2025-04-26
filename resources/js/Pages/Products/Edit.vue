<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from "vue-toastification";
import VueMultiselect from 'vue-multiselect';
import AuthLayout from '@/layouts/AuthLayout.vue';
const toast = useToast();

const props = defineProps({
    categories: Array,
    product: Object,
});

// Get image link function
const getImageLink = (image) => {
    return image ? '/storage/' + image : '/images/placeholder-product.png';
};

// Edit product logic
const editForm = useForm({
    id: props.product.id,
    name: props.product.name,
    description: props.product.description || "",
    price: props.product.price,
    quantity: props.product.quantity,
    min_quantity: props.product.min_quantity,
    category: props.product.categories,
    image: null
});

const editPreviewImage = ref(getImageLink(props.product.image));

const handleEditImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        editForm.image = file;
        editPreviewImage.value = URL.createObjectURL(file);
    }
};

const updateProduct = () => {
    editForm.put(route('products.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Product updated successfully');
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
        },
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Edit Product" />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <H1>Edit Product</H1>
                    <P>Update the product information</P>
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
        </div>

        
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="flex justify-between items-start">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Product</h3>
        </div>
        <div class="mt-4 space-y-4">
            <div>
                <InputLabel for="edit-name" value="Product Name" class="mb-1.5" />
                <TextInput
                    id="edit-name"
                    type="text"
                    class="w-full"
                    v-model="editForm.name"
                    placeholder="Enter product name"
                    :class="{ 'border-red-500': editForm.errors.name }"
                />
                <InputError class="mt-1.5" :message="editForm.errors.name" />
            </div>

            <div>
                <InputLabel for="edit-description" value="Description" class="mb-1.5" />
                <textarea
                    id="edit-description"
                    rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    v-model="editForm.description"
                    placeholder="Enter product description"
                    :class="{ 'border-red-500': editForm.errors.description }"
                ></textarea>
                <InputError class="mt-1.5" :message="editForm.errors.description" />
            </div>

            <div>
                <InputLabel for="edit-image" value="Product Image" class="mb-1.5" />
                <div class="flex items-center gap-4">
                    <div v-if="editPreviewImage" class="flex-shrink-0">
                        <img :src="editPreviewImage" alt="Preview" class="h-16 w-16 object-cover rounded-md border border-gray-200">
                    </div>
                    <label class="cursor-pointer flex-1">
                        <input
                            id="edit-image"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleEditImageChange"
                            :class="{ 'border-red-500': editForm.errors.image }"
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
                <InputError class="mt-1.5" :message="editForm.errors.image" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="edit-price" value="Price" class="mb-1.5" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">MRU</span>
                        </div>
                        <TextInput
                            id="edit-price"
                            type="number"
                            step="0.1"
                            min="0"
                            class="block w-full pl-14"
                            v-model="editForm.price"
                            placeholder="0.0"
                            :class="{ 'border-red-500': editForm.errors.price }"
                        />
                    </div>
                    <InputError class="mt-1.5" :message="editForm.errors.price" />
                </div>

                <div>
                    <InputLabel for="edit-quantity" value="Quantity" class="mb-1.5" />
                    <TextInput
                        id="edit-quantity"
                        type="number"
                        min="0"
                        class="w-full"
                        v-model="editForm.quantity"
                        placeholder="0"
                        :class="{ 'border-red-500': editForm.errors.quantity }"
                    />
                    <InputError class="mt-1.5" :message="editForm.errors.quantity" />
                </div>
            </div>

            <div>
                <InputLabel for="edit-min_quantity" value="Min Quantity" class="mb-1.5" />
                <TextInput
                    id="edit-min_quantity"
                    type="number"
                    step="1"
                    min="1"
                    class="w-full"
                    v-model="editForm.min_quantity"
                    placeholder="1"
                    :class="{ 'border-red-500': editForm.errors.min_quantity }"
                />
                <InputError class="mt-1.5" :message="editForm.errors.min_quantity" />
            </div>

            <div>
                <InputLabel for="edit-category" value="Category" class="mb-1.5" />
                <VueMultiselect
                    v-model="editForm.category"
                    :options="categories"
                    :multiple="true"
                    :close-on-select="true"
                    placeholder="Select category"
                    label="name"
                    track-by="id"
                    :class="{ 'border-red-500': editForm.errors.category }"
                />
                <InputError class="mt-1.5" :message="editForm.errors.category" />
            </div>
        </div>
    </div>
    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <PrimaryButton
            @click="updateProduct"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
            :disabled="editForm.processing"
        >
            <span v-if="!editForm.processing">Update Product</span>
            <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
            </span>
        </PrimaryButton>
        <button
            @click="closeModal"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
        >
            Cancel
        </button>
    </div>
</AuthLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>