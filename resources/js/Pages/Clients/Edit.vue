<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    client: {
        type: Object,
        required: true
    }
});

const toast = useToast();
const form = useForm({
    name: props.client.name,
    number: props.client.number
});

const submit = () => {
    form.put(route('clients.update', props.client.id), {
        onSuccess: () => {
            toast.success('Client mis à jour avec succès');
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
        }
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Modifier Client" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Modifier Client</h1>
                    <p class="text-gray-600 mt-1">Mettre à jour les informations du client</p>
                </div>
                <div>
                    <Link
                        :href="route('clients.index')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour aux Clients
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 max-w-xl">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Nom" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            :class="{ 'ring-1 ring-red-500 mt-2': form.errors.name }"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="number" value="Numéro de Téléphone" />
                        <TextInput
                            id="number"
                            v-model="form.number"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            pattern="^[2-4][0-9]{7}$"
                            title="Le numéro doit commencer par 2, 3 ou 4 et contenir 8 chiffres"
                            :class="{ 'ring-1 ring-red-500 mt-2': form.errors.number }"
                        />
                        <InputError :message="form.errors.number" class="mt-2" />
                        <p>Format : Doit commencer par 2, 3 ou 4 suivi de 7 chiffres</p>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a
                            :href="route('clients.index')"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Annuler
                        </a>
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            <template v-if="form.processing">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mise à jour...
                            </template>
                            <template v-else>
                                Mettre à jour
                            </template>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>
