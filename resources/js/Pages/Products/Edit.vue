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
import DatePicker from 'primevue/datepicker';

const toast = useToast();

const props = defineProps({
    categories: Array,
    product: Object,
    stocks: Array, // Added stocks prop
});



// Edit product logic
const editForm = useForm({
    id: props.product.id,
    name: props.product.name,
    description: props.product.description || "",
    price: props.product.price,
    quantity: props.product.quantity,
    min_quantity: props.product.min_quantity,
    cost: props.product.cost,
    category: props.product.categories,
    stock: props.product.stock ? props.product.stock_two : [],
    expiry_date: props.product.expiry_date ? new Date(props.product.expiry_date) : null, // Added expiry date

});




const updateProduct = () => {
    editForm.put(route('products.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Produit mis à jour avec succès');
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
        <Head title="Modifier Produit" />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <H1>Modifier Produit</H1>
                    <P>Mettre à jour les informations du produit</P>
                </div>
                <div>
                    <Link
                        :href="route('products.index')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour aux Produits
                    </Link>
                </div>
            </div>
        </div>

        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-start">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Modifier Produit</h3>
            </div>
            <div class="mt-4 space-y-4">
                <div>
                    <InputLabel for="edit-name" value="Nom du Produit" class="mb-1.5" />
                    <TextInput
                        id="edit-name"
                        type="text"
                        class="w-full"
                        v-model="editForm.name"
                        placeholder="Entrez le nom du produit"
                        :class="{ 'ring-1 ring-red-500  mt-2': editForm.errors.name }"
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
                        placeholder="Entrez la description du produit"
                        :class="{ 'ring-1 ring-red-500  mt-2': editForm.errors.description }"
                    ></textarea>
                    <InputError class="mt-1.5" :message="editForm.errors.description" />
                </div>



                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="edit-price" value="Prix" class="mb-1.5" />
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">UMR</span>
                            </div>
                            <TextInput
                                id="edit-price"
                                type="number"
                                step="0.1"
                                min="0"
                                class="block w-full pl-14"
                                v-model="editForm.price"
                                placeholder="0.0"
                                :class="{ 'ring-1 ring-red-500  mt-2': editForm.errors.price }"
                            />
                        </div>
                        <InputError class="mt-1.5" :message="editForm.errors.price" />
                    </div>

                    <div>
                        <InputLabel for="edit-quantity" value="Quantité" class="mb-1.5" />
                        <TextInput
                            id="edit-quantity"
                            type="number"
                            min="0"
                            class="w-full"
                            v-model="editForm.quantity"
                            placeholder="0"
                            :class="{ 'ring-1 ring-red-500  mt-2': editForm.errors.quantity }"
                        />
                        <InputError class="mt-1.5" :message="editForm.errors.quantity" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="edit-min_quantity" value="Quantité Minimale" class="mb-1.5" />
                        <TextInput
                            id="edit-min_quantity"
                            type="number"
                            step="1"
                            min="1"
                            class="w-full"
                            v-model="editForm.min_quantity"
                            placeholder="1"
                            :class="{ 'ring-1 ring-red-500  mt-2': editForm.errors.min_quantity }"
                        />
                        <InputError class="mt-1.5" :message="editForm.errors.min_quantity" />
                    </div>
                    <div>
                        <InputLabel for="cost" value="Coût" class="mb-1.5" />
                        <TextInput
                            id="cost"
                            type="number"
                            step="1"
                            min="1"
                            class="w-full"
                            v-model="editForm.cost"
                            placeholder="1"
                            :class="{ 'ring-1 ring-red-500  mt-2': editForm.errors.cost }"
                        />
                        <InputError class="mt-1.5" :message="editForm.errors.cost" />
                    </div>
                </div>

                <!-- Added Date d'expiration Field -->
                <div>
                    <InputLabel for="expiry_date" value="Date d'expiration" class="mb-1.5" />
                    <DatePicker
                        v-model="editForm.expiry_date"
                        class="w-full"
                        :class="{ 'ring-1 ring-red-500 rounded-md mt-2': editForm.errors.expiry_date }"
                        showIcon
                        dateFormat="yy-mm-dd"
                        placeholder="Sélectionnez la date d'expiration"
                    />
                    <InputError class="mt-1.5" :message="editForm.errors.expiry_date" />
                </div>

                <!-- Added Stock Selection Field -->
                <div>
                    <InputLabel for="stock" value="Stock" class="mb-1.5" />
                    <VueMultiselect
                        v-model="editForm.stock"
                        :options="props.stocks"
                        placeholder="Sélectionnez le stock"
                        label="name"
                        track-by="id"
                        :class="{ 'ring-1 ring-red-500 rounded-md mt-2': editForm.errors.stock }"
                    />
                    <InputError class="mt-1.5" :message="editForm.errors.stock" />
                </div>

                <div>
                    <InputLabel for="edit-category" value="Catégorie" class="mb-1.5" />
                    <VueMultiselect
                        v-model="editForm.category"
                        :options="categories"
                        :multiple="true"
                        :close-on-select="true"
                        placeholder="Sélectionnez la catégorie"
                        label="name"
                        track-by="id"
                        :class="{ 'ring-1 ring-red-500  mt-2': editForm.errors.category }"
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
                <span v-if="!editForm.processing">Mettre à jour le Produit</span>
                <span v-else class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mise à jour...
                </span>
            </PrimaryButton>
            <button
                @click="$inertia.get(route('products.index'))"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
                Annuler
            </button>
        </div>
    </AuthLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
