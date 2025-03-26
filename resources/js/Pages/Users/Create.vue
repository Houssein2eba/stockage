<script setup>
import AuthLayout from "@/layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import SelectInput from '@/Components/SelectInput.vue';
import {useToast} from "vue-toastification";
// import 'vue-multiselect/dist/vue-multiselect.css'
const toast = useToast();
defineProps({
  roles: Array,
  
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
    onFinish: () => form.reset(),
    onSuccess: () =>{
      toast.success('User created successfully');
    } ,
  });
};
</script>

<template>
  <AuthLayout>
    <Head title="Create user" />
   
    <div class="max-w-7xl mx-auto mt-4">
        {{roles}}
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
          <InputLabel for="number" value="Number" />

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
          <InputLabel for="password" value="Password" />

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
          <InputLabel for="password_confirmation" value="Confirm Password" />

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
            <InputLabel for="roles" value="Roles" />
            <SelectInput :options="roles" v-model="form.role">

            </SelectInput>
            <InputError
            class="mt-2"
            :message="form.errors.role"
          />
          <span>{{form.errors.role}}</span>
          </div>
          

        <div class="flex items-center justify-end mt-4">
          <PrimaryButton
            class="ml-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Create User
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>