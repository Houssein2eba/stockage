<script setup>
import { cn } from '../utils'

const props = defineProps({
  class: {
    type: String,
    default: '',
  },
  as: {
    type: String,
    default: 'h3',
  },
})
</script>

<template>
  <component
    :is="as"
    :class="
      cn(
        'text-2xl font-semibold leading-none tracking-tight',
        props.class
      )
    "
  >
    <slot />
  </component>
</template>
