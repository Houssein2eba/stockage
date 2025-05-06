<template>
    <VueMultiselect
        v-model="selectedPayment"
        :options="paymentOptions"
        :custom-label="formatPaymentLabel"
        placeholder="Select payment method"
        label="name"
        track-by="id"
        :class="{ 'border-red-500': error }"
        @input="handleSelection"
    >
        <template #option="{ option }">
            <div class="flex items-center gap-2">
                <img
                    v-if="option.logo"
                    :src="option.logo"
                    :alt="option.name + ' logo'"
                    class="w-6 h-6 object-contain"
                >
                <span>{{ option.name }}</span>
            </div>
        </template>
        <template #singleLabel="{ option }">
            <div class="flex items-center gap-2">
                <img
                    v-if="option.logo"
                    :src="option.logo"
                    :alt="option.name + ' logo'"
                    class="w-6 h-6 object-contain"
                >
                <span>{{ option.name }}</span>
            </div>
        </template>
    </VueMultiselect>
</template>

<script setup>
import { ref, computed } from 'vue'
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
    payments: {
        type: Array,
        default: () => []
    },
    modelValue: {
        type: [Object, Number, String],
        default: null
    },
    error: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const selectedPayment = ref(props.modelValue)

const paymentOptions = computed(() => {
    return props.payments.map(payment => ({
        id: payment.id,
        name: payment.name,
        logo: payment.logo_url || null
    }))
})

const formatPaymentLabel = (payment) => {
    return payment.name
}

const handleSelection = (payment) => {
    selectedPayment.value = payment
    emit('update:modelValue', payment ? payment.id : null)
}
</script>
