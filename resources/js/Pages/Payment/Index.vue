<template>
    <AuthLayout>
        <Head title="Settings" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                    <p class="text-gray-600 mt-1">Manage your application settings</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Methods Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Payment Methods</h2>
                            <p class="mt-1 text-sm text-gray-600">Manage the payment methods available in your system.</p>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="payment in props.paymentMethods" :key="payment.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img :src="`/storage/${payment.logo}`" :alt="payment.name" class="h-8 w-8 object-contain" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ payment.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                @click="deletePayment(payment.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >Delete</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!paymentMethods.length">
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            No payment methods found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add Payment Method Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Add Payment Method</h2>
                            <p class="mt-1 text-sm text-gray-600">Add a new payment method to your system.</p>
                        </div>

                        <form @submit.prevent="submitForm" class="p-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="logo" value="Logo" />
                                <div class="mt-1 flex items-center">
                                    <span v-if="logoPreview" class="inline-block h-12 w-12 overflow-hidden rounded-lg bg-gray-100 mr-4">
                                        <img :src="logoPreview" class="h-full w-full object-contain" />
                                    </span>
                                    <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none">
                                        <input 
                                            type="file" 
                                            class="hidden" 
                                            @change="updateLogo"
                                            accept="image/*"
                                        />
                                        <span>Select Logo</span>
                                    </label>
                                </div>
                                <InputError :message="form.errors.logo" class="mt-2" />
                            </div>

                            <div class="flex justify-end">
                                <PrimaryButton 
                                    type="submit"
                                    :class="{ 'opacity-25': form.processing }" 
                                    :disabled="form.processing"
                                >
                                    <span v-if="!form.processing">Add Payment Method</span>
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

<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    paymentMethods: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    name: '',
    logo: null
});

const logoPreview = ref(null);

const updateLogo = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    form.post(route('payment.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            logoPreview.value = null;
            toast.success('Payment method added successfully');
        },
        onError: () => {
            toast.error('Failed to add payment method');
        }
    });
};

const deletePayment = (id) => {
    if (confirm('Are you sure you want to delete this payment method?')) {
        router.delete(route('payment.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Payment method deleted successfully');
            },
            onError: () => {
                toast.error('Failed to delete payment method');
            }
        });
    }
};
</script>