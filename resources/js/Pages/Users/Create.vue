<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

import {useToast} from "vue-toastification";
import MultiSelect from 'vue-multiselect'
const toast = useToast();
const props = defineProps({
  roles: {
    type: Array,
    required: true
  }
})

const form = useForm({
  name: "",
  email: "",
  password: "",
  number:"",
  password_confirmation: "",
  role: "",

});

const submit = () => {
  form.post(route("users.store"), {

    onSuccess: () =>{
      toast.success('Utilisateur créé avec succès');

    } ,
  });
};
</script>

<template>
  <AuthLayout>
    <Head title="Créer un utilisateur" />

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
          <InputLabel for="password" value="Mot de passe" />

          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="new-password"
          />

          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
          <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />

          <TextInput
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password_confirmation"
            required
            autocomplete="new-password"
          />

          <InputError
            class="mt-2"
            :message="form.errors.password_confirmation"
          />
        </div>
        <div class="mt-4">
            <InputLabel for="roles" value="Rôles" />
            <MultiSelect
              v-model="form.role"
              :options="props.roles"
              :multiple="false"
              :close-on-select="true"
              placeholder="Choisir un rôle"
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
            Créer l'utilisateur
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>