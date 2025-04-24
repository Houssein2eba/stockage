<script setup>
import { computed } from 'vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
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

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')} ${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}:${String(d.getSeconds()).padStart(2, '0')}`;
};
</script>

<template>
  <AuthLayout>
    <Head title="Activity Details" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <H1>Activity Details</H1>
          <P >View the details of the activity</P>

        </div>
        <Link
                    :href="route('activity.index')"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Activities
                </Link>
      </div>
      </div>
    <div class="space-y-6">
      <div class="bg-white shadow-sm rounded-lg p-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-medium text-gray-900">{{ activity.description }}</h3>
            <p class="text-sm text-gray-500">
              {{ formatDate(activity.created_at) }}
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
