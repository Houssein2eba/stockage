<template>
    <AuthLayout>
        <Head title="Settings" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Payment Settings</h1>
                    <p class="text-gray-600 mt-1">Manage your payment methods</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 bg-blue-50/80 px-4 py-2 rounded-lg border border-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-blue-800">
                            Total Methods: <span class="font-semibold">{{ props.paymentMethods.length }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Search Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs mb-6">
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    v-model="search"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Search payment methods..."
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Methods Table -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow w-full overflow-x-auto">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Payment Methods</h2>
                            <p class="mt-1 text-sm text-gray-600">Manage the payment methods available in your system.</p>
                        </div>

                        <div class="w-full overflow-x-auto">
                            <Table >
                                <template #header>
                                    <TableRow>
                                        <TableHeaderCell
                                            v-for="header in tableHeaders"
                                            :key="header.field || header.label"
                                            :class="[
                                                header.sortable ? 'cursor-pointer hover:bg-gray-100' : '',
                                                'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
                                                header.label === 'Logo' ? 'w-24' : '',
                                                header.label === 'Actions' ? 'w-24 text-right' : ''
                                            ]"
                                            @click="handleSort(header.field)"
                                        >
                                            <div class="flex items-center space-x-1">
                                                <span>{{ header.label }}</span>
                                                <span v-if="header.sortable" class="flex flex-col">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-3 w-3"
                                                        :class="{'text-blue-600': getSortIcon(header.field) === 'asc'}"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-3 w-3"
                                                        :class="{'text-blue-600': getSortIcon(header.field) === 'desc'}"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </TableHeaderCell>
                                    </TableRow>
                                </template>
                                <template #body>
                                    <TableRow v-for="payment in paymentMethods" :key="payment.id" class="hover:bg-gray-50/50 transition-colors">
                                        <TableDataCell class="px-6 py-4 whitespace-nowrap">
                                            <img :src="`/storage/${payment.logo}`" :alt="payment.name" class="h-8 w-8 object-contain" />
                                        </TableDataCell>
                                        <TableDataCell class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ payment.name }}</div>
                                        </TableDataCell>
                                        <TableDataCell class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button
                                                @click="deletePayment(payment.id)"
                                                class="text-red-600 hover:text-red-900 transition-colors flex items-center gap-1 justify-end w-full"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="hidden sm:inline">Delete</span>
                                            </button>
                                        </TableDataCell>
                                    </TableRow>
                                    <TableRow v-if="paymentMethods.length === 0">
                                        <TableDataCell colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            <div class="flex flex-col items-center justify-center py-6">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                                <p class="mt-2">No payment methods found</p>
                                                <p class="text-sm text-gray-400">Add your first payment method using the form</p>
                                            </div>
                                        </TableDataCell>
                                    </TableRow>
                                </template>
                            </Table>
                        </div>
                    </div>
                </div>

                <!-- Add Payment Form (unchanged) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Payment Method
                            </h2>
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
                                    <span v-if="!form.processing" class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Payment Method
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
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import { ref, watch, computed } from 'vue';
import { useToast } from 'vue-toastification';
import { debounce } from 'lodash';

const toast = useToast();

const props = defineProps({
    paymentMethods: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const form = useForm({
    name: '',
    logo: null
});

const logoPreview = ref(null);
const search = ref(props.filters?.search || '');
const sort = ref({ field: props.filters?.sort || 'created_at', direction: props.filters?.direction || 'desc' });

// Watch for search and sort changes
watch([search, sort], debounce(() => {
    router.get(route('payment.index'), {
        search: search.value,
        sort: sort.value.field,
        direction: sort.value.direction
    }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300), { deep: true });

// Table headers configuration
const tableHeaders = computed(() => [
    { label: 'Logo', field: null, sortable: false },
    { label: 'Name', field: 'name', sortable: true },
    { label: 'Actions', field: null, sortable: false }
]);

const handleSort = (field) => {
    if (!field || !tableHeaders.value.find(header => header.field === field)?.sortable) return;

    if (sort.value.field === field) {
        sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value.field = field;
        sort.value.direction = 'asc';
    }
};

const getSortIcon = (field) => {
    if (!field || sort.value.field !== field) return 'none';
    return sort.value.direction === 'asc' ? 'asc' : 'desc';
};

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
