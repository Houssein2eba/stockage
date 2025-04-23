<script setup>
import { computed } from 'vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
  activity: {
    type: Object,
    required: true,
  },
});

// Helper to format values for display
const formatValue = (value) => {
  if (Array.isArray(value)) return value.join(', ');
  if (value === null) return 'NULL';
  if (value === undefined) return 'UNDEFINED';
  if (typeof value === 'object') return JSON.stringify(value, null, 2);
  return value;
};

const before = computed(() => props.activity.properties?.old || {});
const after = computed(() => props.activity.properties?.attributes || {});
</script>

<template>
  <AuthLayout>
    <div class="space-y-6">
      <div class="bg-white shadow-sm rounded-lg p-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-medium text-gray-900">{{ activity.description }}</h3>
            <p class="text-sm text-gray-500">
              {{ new Date(activity.created_at).toLocaleString() }}
            </p>
          </div>
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h4 class="font-semibold text-gray-700 mb-2">Before</h4>
            <div v-if="Object.keys(before).length">
              <ul class="text-sm text-gray-700 bg-gray-50 rounded p-3">
                <li v-for="(val, key) in before" :key="key">
                  <span class="font-medium">{{ key }}:</span>
                  <span class="ml-2">{{ formatValue(val) }}</span>
                </li>
              </ul>
            </div>
            <div v-else class="text-gray-400 italic">No previous data</div>
          </div>
          <div>
            <h4 class="font-semibold text-gray-700 mb-2">After</h4>
            <div v-if="Object.keys(after).length">
              <ul class="text-sm text-gray-700 bg-gray-50 rounded p-3">
                <li v-for="(val, key) in after" :key="key">
                  <span class="font-medium">{{ key }}:</span>
                  <span class="ml-2">{{ formatValue(val) }}</span>
                </li>
              </ul>
            </div>
            <div v-else class="text-gray-400 italic">No new data</div>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>
