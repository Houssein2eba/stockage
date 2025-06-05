<template>
    <AuthLayout title="Notifications">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Notifications
                </h2>
                <button
                    v-if="unreadCount > 0"
                    @click="markAllAsRead"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
                    Mark all as read
                </button>
            </div>
        </template>

        <div class="py-6 md:py-8">
            <div class="max-w-3xl mx-auto sm:px-4 lg:px-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                    <div v-if="notifications.length > 0" class="divide-y divide-gray-100">
                        <div
                            v-for="notification in notifications"
                            :key="notification.id"
                            :class="[
                                'p-4 transition-colors duration-150',
                                notification.read_at ? 'bg-white' : 'bg-green-300',
                                'hover:bg-gray-50'
                            ]"
                        >
                            <div class="flex gap-3">
                                <div class="flex-shrink-0 pt-0.5">
                                    <div
                                        :class="[
                                            'h-3 w-3 rounded-full',
                                            notification.read_at ? 'bg-gray-300' : 'bg-red-500'
                                        ]"
                                    ></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-2">
                                        <h3 class="text-base font-medium text-gray-900 truncate">
                                            {{ notification.data.title }}
                                        </h3>
                                        <span class="text-xs text-gray-500 whitespace-nowrap">
                                            {{ formatDate(notification.created_at) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ notification.data.message }}
                                    </p>
                                    <div class="mt-2 flex justify-end">
                                        <button
                                            v-if="!notification.read_at"
                                            @click="markAsRead(notification.id)"
                                            class="text-xs font-medium text-blue-600 hover:text-blue-800 px-2 py-1 rounded hover:bg-blue-50 transition-colors"
                                        >
                                            Mark as read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500">We'll notify you when there's something new.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue'
import { format } from 'date-fns'
import { defineProps, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const props = defineProps({
    notifications: {
        type: Array,
        required: true,
    },
})

const toast = useToast()

const unreadCount = computed(() => {
    return props.notifications.filter(n => !n.read_at).length
})

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

const markAllAsRead = () => {
    router.post(route('notifications.markAllAsRead')), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('All notifications marked as read')
        }
    }
}
</script>

<style scoped>
/* Add any custom animations if needed */
</style>
