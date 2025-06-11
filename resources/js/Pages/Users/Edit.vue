<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SelectInput from '@/Components/SelectInput.vue'
import TextInput from '@/Components/TextInput.vue'

import { defineProps } from 'vue'
import { useToast } from 'vue-toastification'
import MultiSelect from 'vue-multiselect'
import { Link, Head, router,useForm } from '@inertiajs/vue3'

const toast = useToast();
const props = defineProps({
  user:Object,
  roles:Array
})

const form = useForm({
  name: "",
  email: "",
  number: "",
  role: null
})
form.name = props.user.name;
form.email = props.user.email;
form.number = props.user.number;
form.role = props.user.roles;

const submit = () => {
  form.put(route('users.update',props.user.id),{
    onSuccess: () => {
      toast.success('Utilisateur mis à jour avec succès');
    },
    onError: (errors) => {
      toast.error('Échec de la mise à jour de l\'utilisateur');
    }
  })
}
</script>

<template>
  <AuthLayout>
    <Head title="Modifier l'utilisateur" />

    <div class="max-w-7xl mx-auto mt-4">
      <div class="flex justify-between">
        <Link
          :href="route('users.index')"
          class="px-3 py-2 text-white font-semibold bg-indigo-500 hover:bg-indigo-700 rounded"
          >Retour</Link
        >
      </div>
    </div>
    <div class="max-w-md mx-auto mt-6 p-6 bg-slate-100">
      <form @submit.prevent="submit">
        <div>
          <InputLabel for="name" value="Nom" />

          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autocomplete="name"
          />

          <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="mt-4">
          <InputLabel for="email" value="Email" />

          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autocomplete="username"
          />

          <InputError class="mt-2" :message="form.errors.email" />
        </div>
        <div class="mt-4">
          <InputLabel for="number" value="Numéro" />

          <TextInput
            id="number"
            type="text"
            class="mt-1 block w-full"
            v-model="form.number"
            required
            autocomplete="number"
          />

          <InputError class="mt-2" :message="form.errors.number" />
        </div>

        <div class="mt-4">
          <InputLabel for="role" value="Rôle" />

          <MultiSelect
            v-model="form.role"
            :options="props.roles"
            :multiple="false"
            :close-on-select="true"
            placeholder="Sélectionner"
            label="name"
            track-by="id"
            class="w-full"
          />
          <InputError class="mt-2" :message="form.errors.role" />
        </div>
        <div class="flex items-center justify-end mt-4">
          <PrimaryButton
            class="ml-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Mettre à jour
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>