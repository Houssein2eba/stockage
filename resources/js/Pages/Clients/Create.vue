<template>
    <AuthLayout>
        <Head title="Create Client" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <H1>Create New Client</H1>
                    <P>Add a new client to your system</P>
                </div>
                <div>
                <Link
                    :href="route('clients.index')"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Clients
                </Link>
                </div>
            </div>

            <div >
                <div class=" bg-white rounded-xl shadow-lg p-8 text-gray-900 lg:w-1/2 sm:w-1/3 mx-auto">
                    <form @submit.prevent="form.post(route('clients.store'), {
                        onSuccess: () => {
                            toast.success('Client created successfully');
                            router.visit(route('clients.index'));
                        },
                        onError: () => {
                            toast.error('Failed to create client');
                        },
                    })" class="space-y-6 max-w-xl">
                        <div>
                            <InputLabel for="name" value="Client Name" class="mb-1.5" />
                            <TextInput
                                id="name"
                                type="text"
                                class="w-full"
                                v-model="form.name"
                                placeholder="Enter client name"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <InputError class="mt-1.5" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="number" value="Client Number" class="mb-1.5" />
                            <TextInput
                                id="number"
                                type="text"
                                class="w-full"
                                v-model="form.number"
                                placeholder="Enter client number"
                                :class="{ 'border-red-500': form.errors.number }"
                            />
                            <InputError class="mt-1.5" :message="form.errors.number" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <Link
                                :href="route('clients.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing" class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Client
                                </span>
                                <span v-else class="flex items-center gap-2">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
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
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const toast = useToast();
const form = useForm({
    name: "",
    number: "",
});
</script>
