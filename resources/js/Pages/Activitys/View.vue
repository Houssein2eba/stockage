<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import Table from '@/Components/Table.vue';
import TableDataCell from '@/Components/TableDataCell.vue';
import TableRow from '@/Components/TableRow.vue';
import TableHeaderCell from '@/Components/TableHeaderCell.vue';

const props = defineProps({
    activity: {
        type: Object,
        required: true
    }
});

defineOptions({
    layout: AuthLayout
});

// Helper function to format values
const formatValue = (value) => {
    if (Array.isArray(value)) return value.join(', ');
    if (value === null) return 'NULL';
    if (value === undefined) return 'UNDEFINED';
    if (typeof value === 'object') return JSON.stringify(value, null, 2);
    return value;
};
</script>

<template>
    <div class="space-y-6">
        
        <div class="bg-white shadow-sm rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ activity.description }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ new Date(activity.created_at).toLocaleString() }}
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                      :class="{
                          'bg-green-100 text-green-800': activity.event === 'created',
                          'bg-blue-100 text-blue-800': activity.event === 'updated',
                          'bg-red-100 text-red-800': activity.event === 'deleted',
                      }">
                    {{ activity.event }}
                </span>
            </div>
        </div>

        <!-- Changes Comparison -->
        <div v-if="activity.properties && (activity.properties.old || activity.properties.new)" 
             class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Old Values -->
            <div v-if="activity.properties.old" class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="bg-red-50 px-4 py-2 border-b border-red-100">
                    <h4 class="font-medium text-red-800">Before Changes</h4>
                </div>
                <Table class="min-w-full">
                    <thead class="bg-gray-50">
                        <TableRow>
                            <TableHeaderCell>Field</TableHeaderCell>
                            <TableHeaderCell>Value</TableHeaderCell>
                        </TableRow>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <TableRow v-for="(value, key) in activity.properties.old" :key="`old-${key}`">
                            <TableDataCell class="font-medium text-gray-700">{{ key }}</TableDataCell>
                            <TableDataCell class="text-gray-600">
                                <pre class="text-xs whitespace-pre-wrap">{{ formatValue(value) }}</pre>
                            </TableDataCell>
                        </TableRow>
                    </tbody>
                </Table>
            </div>

            <!-- New Values -->
            <div v-if="activity.properties.new" class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="bg-green-50 px-4 py-2 border-b border-green-100">
                    <h4 class="font-medium text-green-800">After Changes</h4>
                </div>
                <Table class="min-w-full">
                    <thead class="bg-gray-50">
                        <TableRow>
                            <TableHeaderCell>Field</TableHeaderCell>
                            <TableHeaderCell>Value</TableHeaderCell>
                        </TableRow>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <TableRow v-for="(value, key) in activity.properties.new" :key="`new-${key}`">
                            <TableDataCell class="font-medium text-gray-700">{{ key }}</TableDataCell>
                            <TableDataCell class="text-gray-600">
                                <pre class="text-xs whitespace-pre-wrap">{{ formatValue(value) }}</pre>
                            </TableDataCell>
                        </TableRow>
                    </tbody>
                </Table>
            </div>
        </div>

        
        <div v-else class="bg-white shadow-sm rounded-lg p-4 text-center text-gray-500">
            No property changes recorded for this activity
        </div>
    </div>
</template>