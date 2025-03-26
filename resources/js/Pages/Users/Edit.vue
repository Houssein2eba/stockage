<script setup>

import AuthLayout from '@/layouts/AuthLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SelectInput from '@/Components/SelectInput.vue'
import TextInput from '@/Components/TextInput.vue'

import { defineProps } from 'vue'
import { useToast } from 'vue-toastification'

import { Link, Head, router,useForm } from '@inertiajs/vue3'

const toast = useToast();
const props = defineProps({
  user:Object,
  roles:Array
})
console.log(props.user);
const form = useForm({
  name: props.user.name,
  email: props.user.email,
  number: props.user.number,
  role: props.user.roles[0].name
})

const submit = () => {
  form.put(route('users.update',props.user.id),{
    onSuccess: () => {
      toast.success('User updated successfully');
    },
    onError: (errors) => {
      toast.error('Failed to update user');
    }
  })
}
</script>

<template>
  <AuthLayout>
    <Head title="Create user" />
   
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