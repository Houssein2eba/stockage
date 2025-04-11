<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    price: "",
    min_quantity: "",
    category:""
})
const props = defineProps({
    categories:Array
})

</script>
<template>
  <AuthLayout>
    <Head title="Create product" />
   
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
      <form @submit.prevent="submit">
        <div>
          <InputLabel for="name" value="Name" />

          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
            autocomplete="name"
          />

          <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="mt-4">
          <InputLabel for="price" value="price" />

          <TextInput
            id="price"
            type="number"
            class="mt-1 block w-full"
            v-model="form.price"
            required
            autocomplete="username"
          />

          <InputError class="mt-2" :message="form.errors.price" />
        </div>
        <div class="mt-4">
          <InputLabel for="min_quantity" value="min_quantity" />

          <TextInput
            id="min_quantity"
            type="number"
            class="mt-1 block w-full"
            v-model="form.min_quantity"
            required
            autocomplete="min_quantity"
          />

          <InputError class="mt-2" :message="form.errors.min_quantity" />
        </div>

        <div class="mt-4">
            <InputLabel for="roles" value="Category" />
            <SelectInput :options="categories" v-model="form.category">

            </SelectInput>
            <InputError
            class="mt-2"
            :message="form.errors.category"
          />
          
          </div>
          

        <div class="flex items-center justify-end mt-4">
          <PrimaryButton
            class="ml-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Add Product
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>