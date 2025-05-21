<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import SidebarLink from '@/Components/SidebarLink.vue';
import { useRole } from '@/composables/roles';
import { useAdmin } from '@/composables/admins';
import { format } from 'date-fns';
import { usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';

// Notification System
const showNotifications = ref(false);
const page = usePage();

// Notification state management
const notifications = ref([...page.props.auth.notifications || []]);
const unreadNotificationsCount = ref(page.props.auth.notificationCount || 0);

// Watch for page updates with debouncing
watch(() => page.props.auth.notifications, (newVal) => {
  notifications.value = [...newVal || []];
}, { immediate: true });

watch(() => page.props.auth.notificationCount, (newVal) => {
  unreadNotificationsCount.value = newVal || 0;
}, { immediate: true });

const formatDate = (date) => {
  return format(new Date(date), 'MMM d, yyyy h:mm a');
};

const markAsRead = debounce((id) => {
  router.post(route('notifications.markAsRead', { id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      const notification = notifications.value.find(n => n.id === id);
      if (notification && !notification.read_at) {
        notification.read_at = new Date().toISOString();
        unreadNotificationsCount.value = Math.max(0, unreadNotificationsCount.value - 1);
      }
    }
  });
}, 300);

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  if (showNotifications.value) {
    router.reload({
      only: ['auth.notifications', 'auth.notificationCount'],
      preserveScroll: true,
      preserveState: true,
    });
  }
};

// Sidebar State Management
const isSidebarOpen = ref(false);
const activeDropdown = ref(null);

const dropdowns = {
  users: ref(false),
  posts: ref(false),
  products: ref(false),
  clients: ref(false)
};

const toggleDropdown = (name) => {
  // Close all other dropdowns
  Object.keys(dropdowns).forEach(key => {
    if (key !== name) dropdowns[key].value = false;
  });

  // Toggle current dropdown
  dropdowns[name].value = !dropdowns[name].value;
  activeDropdown.value = dropdowns[name].value ? name : null;
};

// Mobile sidebar handling
watch(isSidebarOpen, (newVal) => {
  if (window.innerWidth < 1024) {
    document.body.style.overflow = newVal ? 'hidden' : 'auto';
  }
});

// Responsive sidebar with debounced resize handler
const handleResize = debounce(() => {
  isSidebarOpen.value = window.innerWidth >= 1024;
}, 100);

onMounted(() => {
  handleResize();
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});

// Composable functions
const { hasRole } = useRole();
const { isAdmin } = useAdmin();

// Notification polling
let pollInterval;
onMounted(() => {
  pollInterval = setInterval(() => {
    router.reload({
      only: ['auth.notifications', 'auth.notificationCount'],
      preserveScroll: true,
      preserveState: true,
    });
  }, 60000);
});

onUnmounted(() => {
  clearInterval(pollInterval);
});
</script>

<template>
  <div class="flex h-screen bg-slate-50">
    <!-- Notification Bell -->
    <div class="fixed top-4 right-4 z-50">
      <button @click="toggleNotifications" class="relative p-2 text-gray-600 hover:text-blue-600 focus:outline-none transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span v-if="unreadNotificationsCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
          {{ unreadNotificationsCount }}
        </span>
      </button>

      <!-- Notifications Dropdown -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-1"
      >
        <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50">
          <div class="p-4 border-b bg-gray-50">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
              <Link :href="route('notifications.index')" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                View All
              </Link>
            </div>
          </div>
          <div class="max-h-96 overflow-y-auto">
            <div v-if="notifications.length > 0" class="divide-y">
              <div v-for="notification in notifications" :key="notification.id"
                   class="p-4 hover:bg-gray-50 transition-colors cursor-pointer"
                   @click="markAsRead(notification.id)">
                <div class="flex items-start">
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ notification.data.title }}</p>
                    <p class="text-sm text-gray-600 truncate">{{ notification.data.message }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ formatDate(notification.created_at) }}</p>
                  </div>
                  <button v-if="!notification.read_at" class="ml-4 text-sm text-blue-600 hover:text-blue-800 transition-colors">
                    Mark as read
                  </button>
                </div>
              </div>
            </div>
            <div v-else class="p-4 text-center text-gray-600">
              No notifications
            </div>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Mobile Backdrop -->
    <Transition
      enter-active-class="transition-opacity ease-linear duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isSidebarOpen "
           @click="isSidebarOpen = false"
           class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden" />
    </Transition>

    <!-- Sidebar -->
    <aside
      class="fixed z-30 top-0 left-0 w-64 h-screen flex flex-col bg-white border-r shadow-xl transition-all duration-300 ease-in-out transform"
      :class="{ '-translate-x-full': !isSidebarOpen, 'translate-x-0': isSidebarOpen }"
    >
      <div class="p-4 border-b bg-gradient-to-r from-blue-600 to-blue-800 shadow-md">
        <h1 class="text-xl font-semibold text-white tracking-wide">Gestion de Stock</h1>
      </div>

      <div class="flex-1 overflow-y-auto">
        <ul class="space-y-1.5 p-3">
          <!-- Dashboard Link -->
          <li>
            <SidebarLink
              href="/dashboard"
              :active="route().current('dashboard')"
              class="hover:bg-blue-50 transition-all duration-200"
            >
              <template #icon>
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                  <path d="M6 8a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V8Z" class="fill-current text-blue-600 group-hover:text-blue-700"/>
                  <path d="M6 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-1Z" class="fill-current text-blue-500 group-hover:text-blue-600"/>
                  <path d="M13 8a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2V8Z" class="fill-current text-blue-400 group-hover:text-blue-500"/>
                  <path d="M13 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-1Z" class="fill-current text-blue-300 group-hover:text-blue-400"/>
                </svg>
              </template>
              <span class="font-medium">Dashboard</span>
            </SidebarLink>
          </li>

          <!-- Users Dropdown -->
          <li v-if="hasRole('admin')">
            <div class="relative">
              <button
                @click="toggleDropdown('users')"
                class="w-full flex items-center justify-between p-2.5 rounded-lg hover:bg-blue-50 transition-colors"
              >
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                  </svg>
                  <span class="font-medium">Users</span>
                </div>
                <svg
                  class="w-4 h-4 transition-transform text-blue-600"
                  :class="{ 'rotate-180': dropdowns.users.value }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <Transition
                enter-active-class="transition-all ease-out duration-200"
                enter-from-class="opacity-0 max-h-0"
                enter-to-class="opacity-100 max-h-40"
                leave-active-class="transition-all ease-in duration-150"
                leave-from-class="opacity-100 max-h-40"
                leave-to-class="opacity-0 max-h-0"
              >
                <div v-if="dropdowns.users.value" class="ml-8 mt-1.5 space-y-1.5 overflow-hidden">
                  <SidebarLink
                    :href="route('users.index')"
                    :active="route().current('users.index')"
                    class="text-sm hover:bg-blue-50"
                  >
                    All Users
                  </SidebarLink>
                  <SidebarLink
                    :href="route('users.create')"
                    :active="route().current('users.create')"
                    class="text-sm hover:bg-blue-50"
                  >
                    Create User
                  </SidebarLink>
                </div>
              </Transition>
            </div>
          </li>

          <!-- Clients Dropdown -->
          <li>
            <div class="relative">
              <button
                @click="toggleDropdown('clients')"
                class="w-full flex items-center justify-between p-2.5 rounded-lg hover:bg-blue-50 transition-colors"
              >
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  <span class="font-medium">Clients</span>
                </div>
                <svg
                  class="w-4 h-4 transition-transform text-blue-600"
                  :class="{ 'rotate-180': dropdowns.clients.value }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <Transition
                enter-active-class="transition-all ease-out duration-200"
                enter-from-class="opacity-0 max-h-0"
                enter-to-class="opacity-100 max-h-40"
                leave-active-class="transition-all ease-in duration-150"
                leave-from-class="opacity-100 max-h-40"
                leave-to-class="opacity-0 max-h-0"
              >
                <div v-if="dropdowns.clients.value" class="ml-8 mt-1.5 space-y-1.5 overflow-hidden">
                  <SidebarLink
                    :href="route('clients.index')"
                    :active="route().current('clients.index')"
                    class="text-sm hover:bg-blue-50"
                  >
                    All Clients
                  </SidebarLink>
                  <SidebarLink
                    :href="route('clients.create')"
                    :active="route().current('clients.create')"
                    class="text-sm hover:bg-blue-50"
                  >
                    Create Client
                  </SidebarLink>
                </div>
              </Transition>
            </div>
          </li>

          <!-- Products Dropdown -->
          <li>
            <div class="relative">
              <button
                @click="toggleDropdown('products')"
                class="w-full flex items-center justify-between p-2.5 rounded-lg hover:bg-blue-50 transition-colors"
              >
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  <span class="font-medium">Products</span>
                </div>
                <svg
                  class="w-4 h-4 transition-transform text-blue-600"
                  :class="{ 'rotate-180': dropdowns.products.value }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <Transition
                enter-active-class="transition-all ease-out duration-200"
                enter-from-class="opacity-0 max-h-0"
                enter-to-class="opacity-100 max-h-40"
                leave-active-class="transition-all ease-in duration-150"
                leave-from-class="opacity-100 max-h-40"
                leave-to-class="opacity-0 max-h-0"
              >
                <div v-if="dropdowns.products.value" class="ml-8 mt-1.5 space-y-1.5 overflow-hidden">
                  <SidebarLink
                    :href="route('categories.index')"
                    :active="route().current('categories.index')"
                    class="text-sm hover:bg-blue-50"
                  >
                    All Categories
                  </SidebarLink>
                  <SidebarLink
                    :href="route('products.index')"
                    :active="route().current('products.index')"
                    class="text-sm hover:bg-blue-50"
                  >
                    All Products
                  </SidebarLink>
                </div>
              </Transition>
            </div>
          </li>

          <!-- Roles Dropdown -->
          <li>
            <div class="relative">
              <button
                @click="toggleDropdown('posts')"
                class="w-full flex items-center justify-between p-2.5 rounded-lg hover:bg-blue-50 transition-colors"
              >
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                  </svg>
                  <span class="font-medium">Roles</span>
                </div>
                <svg
                  class="w-4 h-4 transition-transform text-blue-600"
                  :class="{ 'rotate-180': dropdowns.posts.value }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <Transition
                enter-active-class="transition-all ease-out duration-200"
                enter-from-class="opacity-0 max-h-0"
                enter-to-class="opacity-100 max-h-40"
                leave-active-class="transition-all ease-in duration-150"
                leave-from-class="opacity-100 max-h-40"
                leave-to-class="opacity-0 max-h-0"
              >
                <div v-if="dropdowns.posts.value" class="ml-8 mt-1.5 space-y-1.5 overflow-hidden">
                  <SidebarLink
                    :href="route('roles.index')"
                    :active="route().current('roles.index')"
                    class="text-sm hover:bg-blue-50"
                  >
                    All Roles
                  </SidebarLink>
                  <SidebarLink
                    :href="route('roles.create')"
                    :active="route().current('roles.create')"
                    class="text-sm hover:bg-blue-50"
                  >
                    Create Role
                  </SidebarLink>
                </div>
              </Transition>
            </div>
          </li>
        </ul>
      </div>

      <!-- Logout Button -->
      <div class="p-4 border-t">
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="flex items-center gap-2 w-full p-2.5 rounded-lg hover:bg-blue-50 text-gray-600 transition-colors"
        >
          <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span class="text-sm font-medium">Logout</span>
        </Link>
      </div>
    </aside>

    <!-- Main Content -->
    <div
      class="flex-1 flex flex-col overflow-hidden transition-all duration-300"
      :class="{ 'lg:ml-64': isSidebarOpen }"
    >
      <!-- Top Navigation -->
      <header class="sticky top-0 z-10 bg-white border-b shadow-sm">
        <div class="flex items-center justify-between h-16 px-4">
          <button
            @click="isSidebarOpen = !isSidebarOpen"
            class="p-2 rounded-lg hover:bg-blue-50 transition-colors lg:hidden"
          >
            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <div class="flex items-center gap-2">
            <Link
              v-for="link in [
                { route: 'activity.index', icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', label: 'Activity' },
                { route: 'profile.edit', icon: 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z', label: 'Profile' },
                { route: 'sales.index', icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', label: 'Sales' },
                 {route:'stocks.index', icon: 'M12 14l9-5-9-5-9 5 9 5z', label: 'Stocks' },
              ]"
              :key="link.route"
              :href="route(link.route)"
              class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors"
              :class="{ 'bg-blue-50 text-blue-600': route().current(link.route) }"
            >
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="link.icon" />
              </svg>
              <span class="text-sm font-medium hidden sm:inline">{{ link.label }}</span>
            </Link>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
        <div class="max-w-7xl mx-auto">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<style>
/* Smooth transitions for dropdowns */
.slide-enter-active, .slide-leave-active {
  transition: all 0.2s ease;
}
.slide-enter-from, .slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Better scrollbar styling */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
