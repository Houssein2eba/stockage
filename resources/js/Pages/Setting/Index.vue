<template>
  <AuthLayout>
    <Head title="Enterprise Settings" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Enterprise Settings</h1>
        <p class="mt-1 text-sm text-gray-600">Configure your company information and inventory alerts</p>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Form Section -->
        <div class="p-6 sm:p-8">
          <form @submit.prevent="submit">
            <div class="space-y-6">
              <!-- Company Information Section -->
              <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h2>
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-6">
                    <label for="company-name" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <div class="mt-1">
                      <input
                        type="text"
                        id="company-name"
                        v-model="form.company_name"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      />
                      <InputError class="mt-1" :message="form.errors.company_name" />
                    </div>
                  </div>

                  <div class="sm:col-span-6">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <div class="mt-1">
                      <textarea
                        id="address"
                        v-model="form.address"
                        rows="3"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      ></textarea>
                      <InputError class="mt-1" :message="form.errors.address" />
                    </div>
                  </div>

                  <div class="sm:col-span-3">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1">
                      <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      />
                      <InputError class="mt-1" :message="form.errors.email" />
                    </div>
                  </div>

                  <div class="sm:col-span-3">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <div class="mt-1">
                      <input
                        type="tel"
                        id="phone"
                        v-model="form.phone"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      />
                      <InputError class="mt-1" :message="form.errors.phone" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Inventory Alerts Section -->
              <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inventory Alerts</h2>
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-3">
                    <label for="min_quantity" class="block text-sm font-medium text-gray-700">Low Stock Threshold</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        type="number"
                        id="min_quantity"
                        v-model="form.min_quantity"
                        min="1"
                        class="block w-full rounded-md border-gray-300 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-gray-500 sm:text-sm"> units </span>
                      </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Receive alerts when product quantity falls below this value</p>
                    <InputError class="mt-1" :message="form.errors.min_quantity" />
                  </div>
                </div>
              </div>
            </div>



            <!-- Form Actions -->
            <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 pt-6">
              <button
                type="button"
                @click="reset"
                class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                Reset
              </button>

              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
              >
                <span v-if="!form.processing">Save Settings</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                  </svg>
                  Saving...
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import InputError from '@/Components/InputError.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
  settings: Object
})

const form = useForm({
  company_name: props.settings?.company_name || '',
  address: props.settings?.address || '',
  email: props.settings?.email || '',
  phone: props.settings?.phone || '',
  min_quantity: props.settings?.min_quantity || 5,
})

const submit = () => {
  console.log('Submit function called')
  console.log('Form data:', form.data())
  console.log('Route exists:', typeof route === 'function')

  try {
    const routeUrl = route('settings.update')
    console.log('Route URL:', routeUrl)

    form.put(routeUrl, {
      preserveScroll: true,
      onStart: () => {
        console.log('Request started')
      },
      onProgress: (progress) => {
        console.log('Progress:', progress)
      },
      onSuccess: (page) => {
        console.log('Success:', page)
        toast.success('Settings updated successfully')
      },

        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
                });
            });
            showDeleteModal.value = false;
        },
      onFinish: () => {
        console.log('Request finished')
      }
    })
  } catch (error) {
    console.error('Error in submit:', error)
    toast.error('An unexpected error occurred')
  }
}

const reset = () => {
  form.reset()
  toast.info('Form reset to original values')
}

// Debug: Check if route helper is available
onMounted(() => {
  console.log('Component mounted')
  console.log('Props:', props)
  console.log('Route helper available:', typeof route === 'function')

  if (typeof route === 'function') {
    try {
      console.log('Settings update route:', route('settings.update'))
    } catch (e) {
      console.error('Route error:', e)
    }
  }
})
</script>
