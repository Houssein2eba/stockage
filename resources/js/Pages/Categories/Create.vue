<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const form = useForm({
    name: "",
    description:""
})

const toast = useToast();
</script>
<template>
  <AuthLayout>
    <Head title="Create Category" />
   
    <div class="max-w-7xl mx-auto mt-4">
       
      <div class="flex justify-between">
        <Link
          :href="route('users.index')"
          class="px-3 py-2 text-white font-semibold bg-indigo-500 hover:bg-indigo-700 rounded"
          >Back</Link
        >
      </div>
    </div>
    <div class="max-w-md mx-auto mt-6 p-6 bg-slate-100">
      <form @submit.prevent="form.post(route('categories.store'), {
         onSuccess: () => {
         toast.success('Category created successfully');
          form.reset();
    },
    onError: () => {
         toast.error('Failed to create category');
    },
})">
        <div>
          <InputLabel for="name" value="Name" />

          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            
            autofocus
            autocomplete="name"
          />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
<div class="mt-4">
          <InputLabel for="description" value="Description" />

          <TextInput
            id="description"
            type="text"
            class="mt-1 block w-full"
            v-model="form.description"
            
            autofocus
            autocomplete="description"
          />
          <InputError class="mt-2" :message="form.errors.description" />
        </div>
        <div class="flex items-center justify-end mt-4">
          <PrimaryButton
            class="ml-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Add Ctegory
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>