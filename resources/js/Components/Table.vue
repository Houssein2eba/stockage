<script setup>
</script>
<template>
  <div class="max-w-fit m-auto text-center border borde-2 border-black p-4 bg-white">
    <table class="w-7xl text-sm text-left text-gray-500 dark:text-gray-400">
      <thead
        class="text-xs  uppercase bg-gray-500 dark:bg-gray-700 dark:text-gray-400"
      >
        <slot name="header"></slot>
      </thead>
      <tbody>
        <slot></slot>
      </tbody>
    </table>
  </div>
</template>