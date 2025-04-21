<template>
  <div class="min-w-full inline-block align-middle">
    <div class="overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <slot name="header"></slot>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <slot></slot>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
</script>