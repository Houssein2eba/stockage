<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import SidebarLink from '@/Components/SidebarLink.vue';
import { usePermission } from '@/composables/permissions';
import { useAdmin } from '@/composables/admins';

// Reactive state
const isSidebarOpen = ref(false);
const isUsersDropdownOpen = ref(false);
const isPostsDropdownOpen = ref(false);
const isProductsDropdownOpen = ref(false);
const isClientsDropdownOpen = ref(false);
// Composable functions
const { hasPermission } = usePermission();
const { hasRole } = usePermission();
const { isAdmin } = useAdmin();

// Toggle functions
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleUsersDropdown = () => {
  isUsersDropdownOpen.value = !isUsersDropdownOpen.value;
  isPostsDropdownOpen.value = false;
  isProductsDropdownOpen.value = false;
    isClientsDropdownOpen.value = false;
};

const togglePostsDropdown = () => {
  isPostsDropdownOpen.value = !isPostsDropdownOpen.value;
  isUsersDropdownOpen.value = false;
  isProductsDropdownOpen.value = false;
    isClientsDropdownOpen.value = false;
};
const toggleProductsDropdown = () => {
  isProductsDropdownOpen.value = !isProductsDropdownOpen.value;
  isUsersDropdownOpen.value = false;
  isPostsDropdownOpen.value = false;
    isClientsDropdownOpen.value = false;
};
const toggleClientsDropdown = () => {
  isClientsDropdownOpen.value = !isClientsDropdownOpen.value;
  isUsersDropdownOpen.value = false;
  isPostsDropdownOpen.value = false;
    isProductsDropdownOpen.value = false;
};

// Handle body overflow on mobile
watch(isSidebarOpen, (newVal) => {
  if (window.innerWidth < 1024) {
    document.body.style.overflow = newVal ? 'hidden' : 'auto';
  }
});

// Close sidebar on larger screens
onMounted(() => {
  const handleResize = () => {
    if (window.innerWidth >= 1024) {
      isSidebarOpen.value = true;
    } else {
      isSidebarOpen.value = false;
    }
  };

  handleResize();
  window.addEventListener('resize', handleResize);
});
</script>

<template>
  <div class="flex h-screen bg-slate-50">
    <!-- Mobile Backdrop -->
    <div
      v-if="isSidebarOpen"
      @click="toggleSidebar"
      class="fixed inset-0 z-20 bg-gray-900/50 backdrop-blur-sm lg:hidden"
    />

    <!-- Sidebar -->
    <aside
      class="fixed z-30 top-0 left-0 w-64 h-screen flex flex-col bg-white border-r shadow-xl transition-transform duration-300 lg:translate-x-0"
      :class="{ '-translate-x-full': !isSidebarOpen }"
    >
      <div class="p-4 border-b bg-gradient-to-r from-blue-600 to-blue-800 shadow-md transition-all duration-300 hover:from-blue-700 hover:to-blue-900">
        <h1 class="text-xl font-semibold text-white tracking-wide">Gestion de Stock</h1>
      </div>

      <div class="flex-1 overflow-y-auto">
        <ul class="space-y-1.5 p-3">
          <!-- Dashboard Link -->
          <li class="group">
            <SidebarLink
              href="/dashboard"
              :active="route().current('dashboard')"
              class="hover:bg-blue-50 transition-all duration-200 shadow-sm hover:shadow-md"
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
          <li class="group">
            <div class="relative">
              <button
                @click="toggleUsersDropdown"
                class="w-full flex items-center justify-between p-2.5 rounded-lg hover:bg-blue-50 transition-all duration-200 shadow-sm hover:shadow-md"
              >
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                  </svg>
                  <span class="font-medium">Users</span>
                </div>
                <svg
                  class="w-4 h-4 transition-transform text-blue-600"
                  :class="{ 'rotate-180': isUsersDropdownOpen }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <div v-if="isUsersDropdownOpen"
                   class="ml-8 mt-1.5 space-y-1.5 overflow-hidden transition-all duration-200"
                   style="animation: slideDown 0.2s ease-out"
              >
                <SidebarLink
                  :href="route('users.index')"
                  :active="route().current('users.index')"
                  class="text-sm hover:bg-blue-50"
                >
                  All Users
                </SidebarLink>
                <SidebarLink
                  v-if="isAdmin || hasPermission('create_users')"
                  :href="route('users.create')"
                  :active="route().current('users.create')"
                  class="text-sm hover:bg-blue-50"
                >
                  Create User
                </SidebarLink>
              </div>
            </div>
          </li>
          <!-- Clients dropdown -->
<li>
  <div class="relative">
    <button
      @click="toggleClientsDropdown"
      class="w-full flex items-center justify-between p-2.5 rounded-lg hover:bg-blue-50 transition-colors group shadow-sm hover:shadow-md"
    >
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
        <span class="font-medium">Clients</span>
      </div>
      <svg
        class="w-4 h-4 transition-transform text-blue-600"
        :class="{ 'rotate-180': isClientsDropdownOpen }"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>
    <div v-if="isClientsDropdownOpen" class="ml-8 mt-1.5 space-y-1.5">
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
  </div>
</li>

<!-- Roles Dropdown -->
<li>
  <div class="relative">
    <button
      @click="togglePostsDropdown"
      class="w-full shadow-sm hover:shadow-md flex items-center justify-between p-2.5 rounded-lg hover:bg-blue-50 transition-colors group"
    >
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
        </svg>
        <span class="font-medium">Roles</span>
      </div>
      <svg
        class="w-4 h-4 transition-transform text-blue-600"
        :class="{ 'rotate-180': isPostsDropdownOpen }"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>
    <div v-if="isPostsDropdownOpen" class="ml-8 mt-1.5 space-y-1.5">
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
  </div>
</li>

<!-- Products Dropdown -->
<li>
  <div class="relative">
    <button
      @click="toggleProductsDropdown"
      class="w-full shadow-sm hover:shadow-md flex items-center justify-between p-2.5 rounded-lg hover:bg-blue-50 transition-colors group"
    >
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <span class="font-medium">Products</span>
      </div>
      <svg
        class="w-4 h-4 transition-transform text-blue-600"
        :class="{ 'rotate-180': isProductsDropdownOpen }"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>
    <div v-if="isProductsDropdownOpen" class="ml-8 mt-1.5 space-y-1.5">
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
          class="flex items-center gap-2 w-full p-2.5 rounded-lg hover:bg-blue-50 text-gray-600 transition-colors group"
        >
          <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span class="text-sm font-medium group-hover:text-blue-600">Logout</span>
        </Link>
      </div>
    </aside>

    <!-- Main Content -->
    <div
      class="flex-1 flex flex-col overflow-hidden transition-all duration-300"
      :class="{ 'lg:ml-64': isSidebarOpen }"
    >
      <!-- Top Navigation -->
      <header class="sticky top-0 z-20 bg-white border-b shadow-sm">
        <div class="flex items-center justify-between h-16 px-4">
          <button
            @click="toggleSidebar"
            class="p-2 rounded-lg hover:bg-blue-50 transition-colors lg:hidden group"
          >
            <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <div class="flex items-center gap-3">
            <Link
              :href="route('activity.index')"
              class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors group"
              :class="{ 'bg-blue-50 text-blue-600': route().current('activity.index') }"
            >
              <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              <span class="text-sm font-medium">Activity</span>
            </Link>

            <Link
              :href="route('profile.edit')"
              class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors group"
              :class="{ 'bg-blue-50 text-blue-600': route().current('profile.edit') }"
            >
              <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-sm font-medium">Profile</span>
            </Link>

            <Link
              :href="route('sales.index')"
              class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors group"
              :class="{ 'bg-blue-50 text-blue-600': route().current('sales.index') }"
            >
              <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span class="text-sm font-medium">Sell</span>
            </Link>

            <Link
              :href="route('payment.index')"
              class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors group"
              :class="{ 'bg-blue-50 text-blue-600': route().current('payment.index') }"
            >
              <svg class="w-5 h-5 text-blue-600 group-hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-sm font-medium">Payment</span>
            </Link>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-blue-50/30 p-4 md:p-6">
        <div class="max-w-7xl mx-auto">
          <div class="bg-white/80 backdrop-blur-sm shadow-lg rounded-xl p-6">
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style>
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group:hover .group-hover\:shadow-md {
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}
</style>
