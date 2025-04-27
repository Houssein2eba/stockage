<template>
    <AuthLayout title="Notifications">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifications
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="notifications.length > 0">
                        <div v-for="notification in notifications" :key="notification.id" 
                             class="mb-4 p-4 border rounded-lg hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ notification.data.title }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ notification.data.message }}
                                    </p>
                                    <span class="text-xs text-gray-500">
                                        {{ formatDate(notification.created_at) }}
                                    </span>
                                </div>
                                <button @click="markAsRead(notification.id)" 
                                        v-if="!notification.read_at"
                                        class="text-sm text-blue-600 hover:text-blue-800">
                                    Mark as read
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-600">
                        No notifications found
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { format } from 'date-fns'
import { defineProps } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
const props = defineProps({
    notifications: {
        type: Array,
        required: true,
    },
})
const toast = useToast()
const formatDate = (date) => {
    return format(new Date(date), 'MMM d, yyyy h:mm a')
}

const markAsRead = (id) => {
    router.post(route('notifications.markAsRead', { id }), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Notification marked as read')
        }
    })
}
</script>
