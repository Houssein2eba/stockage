<template>
  <div class="w-full overflow-x-auto">
    <table class="w-full table-auto border-collapse">
      <thead>
        <slot name="header"></slot>
      </thead>
      <tbody class="bg-white">
        <slot></slot>
      </tbody>
    </table>
  </div>
</template>

<script setup>
</script>