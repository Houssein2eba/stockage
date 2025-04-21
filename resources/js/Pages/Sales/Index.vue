<template>
    <AuthLayout>
      <Head title="Create Sale" />

      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Sale</h1>
            <p class="text-gray-600 mt-1">Record a new sales transaction</p>
          </div>
        </div>

        <!-- Sales Form -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-medium text-gray-900">Sale Information</h2>
          </div>

          <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <!-- Client Selection (Optional) -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <InputLabel for="client" value="Client (Optional)" />
                <button
                  type="button"
                  @click="showClientModal = true"
                  class="text-sm text-blue-600 hover:text-blue-800"
                >
                  + Add New Client
                </button>
              </div>
              <VueMultiselect
                v-model="form.client"
                :options="clients"
                :searchable="true"
                placeholder="Select client"
                label="name"
                track-by="id"
                :class="{ 'border-red-500': form.errors.client }"
              >
                <template #option="{ option }">
                  <div class="flex items-center">
                    <span class="text-gray-900">{{ option.name }}</span>
                    <span class="ml-2 text-xs text-gray-500">{{ option.email }}</span>
                  </div>
                </template>
              </VueMultiselect>
              <InputError class="mt-1" :message="form.errors.client" />
            </div>

            <!-- Sale Date -->
            <div>
              <InputLabel for="date" value="Sale Date" />
              <TextInput
                id="date"
                type="date"
                class="w-full"
                v-model="form.date"
                :max="new Date().toISOString().split('T')[0]"
                :class="{ 'border-red-500': form.errors.date }"
              />
              <InputError class="mt-1" :message="form.errors.date" />
            </div>

            <!-- Products Selection -->
            <div>
              <InputLabel value="Products" />
              <div class="space-y-4">
                <div
                  v-for="(item, index) in form.items"
                  :key="index"
                  class="p-4 border border-gray-200 rounded-lg"
                >
                  <div class="grid grid-cols-12 gap-4">
                    <!-- Product Select -->
                    <div class="col-span-8">
                      <VueMultiselect
                        v-model="item.product"
                        :options="products"
                        :searchable="true"
                        placeholder="Select product"
                        label="name"
                        track-by="id"
                        @select="updateProductDetails(index)"
                        :class="{ 'border-red-500': form.errors[`items.${index}.product_id`] }"
                      >
                        <template #option="{ option }">
                          <div class="flex items-center">
                            <img
                              :src="getImageLink(option.image)"
                              class="w-8 h-8 rounded-md object-cover mr-2"
                              alt="Product image"
                            >
                            <div>
                              <div class="text-gray-900">{{ option.name }}</div>
                              <div class="text-xs text-gray-500">
                                {{ formatPrice(option.price) }} • {{ option.quantity }} in stock
                              </div>
                            </div>
                          </div>
                        </template>
                      </VueMultiselect>
                      <InputError
                        class="mt-1"
                        :message="form.errors[`items.${index}.product_id`] ||
                                 form.errors[`items.${index}.product`]"
                      />
                    </div>

                    <!-- Quantity -->
                    <div class="col-span-2">
                      <TextInput
                        type="number"
                        min="1"
                        :max="item.product ? item.product.quantity : null"
                        v-model="item.quantity"
                        @change="validateQuantity(index)"
                        placeholder="Qty"
                        :class="{ 'border-red-500': form.errors[`items.${index}.quantity`] }"
                      />
                      <InputError class="mt-1" :message="form.errors[`items.${index}.quantity`]" />
                    </div>

                    <!-- Price -->
                    <div class="col-span-2 flex items-center">
                      <span class="text-gray-700 font-medium">
                        {{ item.product ? formatPrice(item.product.price * item.quantity) : '--' }}
                      </span>
                    </div>
                  </div>

                  <!-- Remove button -->
                  <div class="mt-3 flex justify-end">
                    <button
                      type="button"
                      @click="removeItem(index)"
                      class="text-red-600 text-sm hover:text-red-800 flex items-center"
                      :disabled="form.items.length <= 1"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Remove
                    </button>
                  </div>
                </div>

                <!-- Add Product button -->
                <button
                  type="button"
                  @click="addItem"
                  class="w-full py-2 px-4 border border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors flex items-center justify-center text-blue-600"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                  </svg>
                  Add Product
                </button>
              </div>
            </div>

            <!-- Payment Information -->
            <div class="pt-4 border-t border-gray-200">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Method -->
                <div>
                  <InputLabel for="payment_method" value="Payment Method" />
                  <select
                    id="payment_method"
                    v-model="form.payment_method"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-500': form.errors.payment_method }"
                  >
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                  </select>
                  <InputError class="mt-1" :message="form.errors.payment_method" />
                </div>

                <!-- Payment Status -->
                <div>
                  <InputLabel for="payment_status" value="Payment Status" />
                  <select
                    id="payment_status"
                    v-model="form.payment_status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-500': form.errors.payment_status }"
                  >
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="partially_paid">Partially Paid</option>
                  </select>
                  <InputError class="mt-1" :message="form.errors.payment_status" />
                </div>
              </div>

              <!-- Amount Paid -->
              <div class="mt-4">
                <InputLabel for="amount_paid" :value="`Amount Paid (${form.payment_status === 'partially_paid' ? 'Partial' : 'Full'})`" />
                <div class="relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500">MRU</span>
                  </div>
                  <TextInput
                    id="amount_paid"
                    type="number"
                    step="0.01"
                    min="0"
                    class="block w-full pl-12"
                    v-model="form.amount_paid"
                    :placeholder="form.payment_status === 'partially_paid' ? 'Enter partial amount' : 'Enter full amount'"
                    :class="{ 'border-red-500': form.errors.amount_paid }"
                  />
                </div>
                <InputError class="mt-1" :message="form.errors.amount_paid" />
              </div>
            </div>

            <!-- Summary -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium">{{ formatPrice(subtotal) }}</span>
              </div>
              <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600">Tax (10%):</span>
                <span class="font-medium">{{ formatPrice(tax) }}</span>
              </div>
              <div class="flex justify-between items-center border-t border-gray-200 pt-2 mt-2">
                <span class="text-gray-900 font-medium">Total:</span>
                <span class="text-lg font-bold text-blue-600">{{ formatPrice(total) }}</span>
              </div>
            </div>

            <!-- Notes -->
            <div>
              <InputLabel for="notes" value="Notes (Optional)" />
              <textarea
                id="notes"
                rows="3"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                v-model="form.notes"
                placeholder="Any additional notes about this sale..."
              ></textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-4">
              <Link
                :href="route('sales.index')"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Cancel
              </Link>
              <PrimaryButton
                type="submit"
                class="px-4 py-2 text-sm font-medium"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                <span v-if="!form.processing">Create Sale</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </span>
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>

      <!-- Client Modal -->
      <Modal :show="showClientModal" @close="showClientModal = false">
        <div class="p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Add New Client</h2>
          <!-- Client form would go here -->
          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="showClientModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <PrimaryButton @click="saveClient">
              Save Client
            </PrimaryButton>
          </div>
        </div>
      </Modal>
    </AuthLayout>
  </template>

  <script setup>
  import { ref, computed } from 'vue';
  import { useForm, Link } from '@inertiajs/vue3';
  import AuthLayout from '@/layouts/AuthLayout.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';
  import Modal from '@/Components/Modal.vue';
  import VueMultiselect from 'vue-multiselect';

  const props = defineProps({
    clients: {
      type: Array,
      default: () => []
    },
    products: {
      type: Array,
      default: () => []
    }
  });
  
  const showClientModal = ref(false);

  const form = useForm({
    client: null,
    date: new Date().toISOString().split('T')[0],
    items: [{ product: null, quantity: 1 }],
    payment_method: 'cash',
    payment_status: 'paid',
    amount_paid : 0,
    notes: '',
  });

  const subtotal = computed(() => {
    return form.items.reduce((sum, item) => {
      return sum + (item.product?.price || 0) * (item.quantity || 0);
    }, 0);
  });

  const tax = computed(() => {
    return subtotal.value * 0.1; // 10% tax
  });

  const total = computed(() => {
    return subtotal.value + tax.value;
  });

  const addItem = () => {
    form.items.push({ product: null, quantity: 1 });
  };

  const removeItem = (index) => {
    if (form.items.length > 1) {
      form.items.splice(index, 1);
    }
  };

  const updateProductDetails = (index) => {
    if (form.items[index]?.product) {
      form.items[index].quantity = 1;
      validateQuantity(index);
    }
  };

  const validateQuantity = (index) => {
    const item = form.items[index];
    if (item?.product && item.quantity > item.product.quantity) {
      form.items[index].quantity = item.product.quantity;
    }
  };

  const submitForm = () => {
    const submissionData = {
      client_id: form.client?.id || null,
      date: form.date,
      payment_method: form.payment_method,
      payment_status: form.payment_status,
      amount_paid: parseFloat(form.amount_paid) || 0,
      notes: form.notes,
      items: form.items.map(item => ({
        product_id: item.product?.id,
        quantity: parseInt(item.quantity) || 1,
        unit_price: item.product?.price || 0
      }))
    };

    form.transform(() => submissionData).post(route('sales.store'), {
      onSuccess: () => {
        form.reset();
        form.items = [{ product: null, quantity: 1 }];
      },
      onError: (errors) => {
        console.error('Form submission error:', errors);
      }
    });
  };

  const saveClient = () => {
    // Implement client saving logic here
    showClientModal.value = false;
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'MRU'
    }).format(price || 0);
  };

  const getImageLink = (image) => {
    return image ? '/storage/' + image : '/images/placeholder-product.png';
  };
  </script>

  <style src="vue-multiselect/dist/vue-multiselect.css"></style>

  <style>
  /* Custom styles for multiselect */
  .multiselect {
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
  }

  .multiselect--active {
    border-color: #3b82f6;
    box-shadow: 0 0 0 1px #3b82f6;
  }

  .multiselect__tags {
    border: none;
    padding: 0.5rem 1rem;
  }

  .multiselect__option--highlight {
    background: #3b82f6;
  }

  .multiselect__option--selected {
    background: #e0e7ff;
    color: #1e40af;
  }

  /* Error state */
  .border-red-500 {
    border-color: #ef4444;
  }
  </style>
