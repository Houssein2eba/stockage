<template>
  <AuthLayout>
    <Head title="Create New Stock Location" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Create New Stock Location</h1>
          <p class="text-sm text-gray-500">Add a new inventory storage location</p>
        </div>
        <div>
          <Link
            :href="route('stocks.index')"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
            preserve-scroll
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Stocks
          </Link>
        </div>
      </div>

      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Form Header -->
        <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-blue-600 to-blue-800">
          <h3 class="text-lg leading-6 font-medium text-white">
            Stock Location Information
          </h3>
        </div>

        <!-- Form Content -->
        <form @submit.prevent="submit">
          <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                <label for="name" class="block">Stock Name *</label>
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500': form.errors.name }"
                  placeholder="e.g. Main Warehouse"
                />
                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                  {{ form.errors.name }}
                </p>
              </dd>
            </div>

            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                <label for="location" class="block">Location *</label>
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <input
                  type="text"
                  id="location"
                  v-model="form.location"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500': form.errors.location }"
                  placeholder="e.g. Building A, Floor 2"
                />
                <p v-if="form.errors.location" class="mt-2 text-sm text-red-600">
                  {{ form.errors.location }}
                </p>
              </dd>
            </div>




          </div>

          <!-- Form Footer -->
          <div class="px-4 py-4 bg-gray-50 text-right sm:px-6 border-t border-gray-200">
            <button
              type="button"
              @click="reset"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Reset
            </button>
            <button
              type="submit"
              class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Create Stock Location
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import {useToast} from 'vue-toastification'
const toast = useToast()
const form = useForm({
  name: '',
  location: '',
  status: 'empty',

})

const submit = () => {
      form.post(route('stocks.store'), {
        onSuccess: () => {
            toast.success('Stock created successfully')
            form.reset()
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                })
            })
        }
    })
}

const reset = () => {
  form.reset()
}
</script>
