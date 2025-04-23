<template>
  
    <table class="w-full table-auto border-collapse">
      <thead class="bg-gray-50">
        <slot name="header">

        </slot>
      </thead>
      <tbody class="bg-white">
        <slot name="body"></slot>
      </tbody>
    </table>
  
</template>

<script setup>
</script>