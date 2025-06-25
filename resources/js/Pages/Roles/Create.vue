<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import {Head, Link} from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { useToast } from "vue-toastification"
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import VueMultiselect from 'vue-multiselect'
import { computed } from 'vue'

const props = defineProps({
    permissions: {
        type: Array,
        required: true
    }
});

const toast = useToast();
const form = useForm({
    name: '',
    permissions: []
});

const formatPermissionLabel = (permissionName) => {
  const translations = {
    // Clients
    'voirClients': 'Voir les clients',
    'creerClients': 'Créer des clients',
    'exporterClients': 'Exporter les clients',
    'modifierClients': 'Modifier les clients',
    'supprimerClients': 'Supprimer des clients',
    
    // Ventes
    'voirVentes': 'Voir les ventes',
    'creerVentes': 'Créer des ventes',
    'marquerPaye': 'Marquer comme payé',
    'modifierVentes': 'Modifier les ventes',
    'supprimerVentes': 'Supprimer des ventes',
    'genererFacture': 'Générer une facture',
    
    // Stocks
    'voirStocks': 'Voir les stocks',
    'creerStocks': 'Créer des stocks',
    'modifierStocks': 'Modifier les stocks',
    'supprimerStocks': 'Supprimer des stocks',
    
    // Utilisateurs
    'voirUtilisateurs': 'Voir les utilisateurs',
    'creerUtilisateurs': 'Créer des utilisateurs',
    'modifierUtilisateurs': 'Modifier les utilisateurs',
    'supprimerUtilisateurs': 'Supprimer des utilisateurs',
    'exporterUtilisateurs': 'Exporter les utilisateurs',
    
    // Journal
    'voirJournal': 'Voir le journal',
    
    // Produits
    'voirProduits': 'Voir les produits',
    'creerProduits': 'Créer des produits',
    'exporterProduits': 'Exporter les produits',
    'modifierProduits': 'Modifier les produits',
    'supprimerProduits': 'Supprimer des produits',
    
    // Catégories
    'voirCategories': 'Voir les catégories',
    'creerCategories': 'Créer des catégories',
    'modifierCategories': 'Modifier les catégories',
    'supprimerCategories': 'Supprimer des catégories',
    
    // Notifications
    'voirNotifications': 'Voir les notifications'
  };

  return translations[permissionName] || permissionName;
};

// Formate les permissions pour le multiselect
const formattedPermissions = computed(() => {
  return props.permissions.map(permission => ({
    ...permission,
    formattedName: formatPermissionLabel(permission.name)
  }));
});

const submit = () => {
  form.post(route('roles.store'), {
    onSuccess: () => {
      toast.success('Rôle créé avec succès');
      form.reset();
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
        <Head title="Créer le Rôle" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <H1>Créer un Rôle</H1>
                    <P>Créer un nouveau rôle avec des permissions</P>
                </div>
                <div>
                    <Link
                        :href="route('roles.index')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour aux Rôles
                    </Link>
                </div>
            </div>

            <div class="m-auto w-3/4 sm:w-1/2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="Nom du Rôle" />
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
                            <InputLabel value="Permissions" />
                            <VueMultiselect
                                v-model="form.permissions"
                                :options="formattedPermissions"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :searchable="true"
                                track-by="id"
                                label="formattedName"
                                :placeholder="formattedPermissions.length > 0 ? 'Sélectionner des permissions' : 'Aucune permission disponible'"
                                :class="{ 'border-red-500': form.errors.permissions }"
                            />
                            <InputError :message="form.errors.permissions" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <PrimaryButton
                                type="submit"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700"
                            >
                                <span v-if="!form.processing" class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Créer le Rôle
                                </span>
                                <span v-else class="flex items-center gap-2">
                                    <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Traitement en cours...
                                </span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style>
.multiselect__tags {
    border-radius: 0.375rem;
    min-height: 42px;
    border: 1px solid #d1d5db;
}
.multiselect__tag {
    background: #3b82f6;
}
.multiselect__tag-icon:after {
    color: white;
}
.multiselect__tag-icon:hover {
    background: #2563eb;
}
.multiselect__option--highlight {
    background: #3b82f6;
}
.multiselect__option--selected.multiselect__option--highlight {
    background: #ef4444;
}
</style>