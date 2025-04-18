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
  <div class="flex h-screen bg-gray-50">
    <!-- Mobile Backdrop -->
    <div
      v-if="isSidebarOpen"
      @click="toggleSidebar"
      class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
    />

    <!-- Sidebar -->
    <aside
      class="fixed z-30 top-0 left-0 w-64 h-screen flex flex-col bg-white border-r shadow-lg transition-transform duration-300 lg:translate-x-0"
      :class="{ '-translate-x-full': !isSidebarOpen }"
    >
      <div class="p-4 border-b">
        <h1 class="text-xl font-semibold">Gestion de Stock</h1>
      </div>

      <div class="flex-1 overflow-y-auto">
        <ul class="space-y-1 p-2">
          <!-- Dashboard Link -->
          <li>
            <SidebarLink
              href="/dashboard"
              :active="route().current('dashboard')"
              class="hover:bg-gray-100"
            >
              <template #icon>
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                  <path d="M6 8a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V8Z" class="fill-current text-blue-500"/>
                  <path d="M6 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-1Z" class="fill-current text-blue-500"/>
                  <path d="M13 8a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2V8Z" class="fill-current text-blue-300"/>
                  <path d="M13 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-1Z" class="fill-current text-blue-400"/>
                </svg>
              </template>
              <span>Dashboard</span>
            </SidebarLink>
          </li>

          <!-- Users Dropdown -->
          <li >
            <div class="relative">
              <button
                @click="toggleUsersDropdown"
                class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                  </svg>
                  <span class="text-sm font-medium">Users</span>
                </div>
                <svg
                  class="w-4 h-4 transition-transform text-gray-400"
                  :class="{ 'rotate-180': isUsersDropdownOpen }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <div v-if="isUsersDropdownOpen" class="ml-8 mt-1 space-y-1">
                <SidebarLink
                  :href="route('users.index')"
                  :active="route().current('users.index')"
                  class="text-sm"
                >
                  All Users
                </SidebarLink>
                <SidebarLink
                  v-if="isAdmin || hasPermission('create_users')"
                  :href="route('users.create')"
                  :active="route().current('users.create')"
                  class="text-sm"
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
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 transition-colors"
                >
                    <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span class="text-sm font-medium">Clients</span>
                    </div>
                    <svg
                    class="w-4 h-4 transition-transform text-gray-400"
                    :class="{ 'rotate-180': isClientsDropdownOpen }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div v-if="isClientsDropdownOpen" class="ml-8 mt-1 space-y-1">
                    <SidebarLink
                    :href="route('clients.index')"
                    :active="route().current('clients.index')"
                    class="text-sm"
                    >
                    All Clients
                    </SidebarLink>
                    <SidebarLink
                    
                    :href="route('clients.create')"
                    :active="route().current('clients.create')"
                    class="text-sm"
                    >
                    Create Client
                    </SidebarLink>
                </div>
                </div>
            </li>

          <!-- Posts Dropdown -->
          <li>
            <div class="relative">
              <button
                @click="togglePostsDropdown"
                class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                  </svg>
                  <span class="text-sm font-medium">Roles</span>
                </div>
                <svg
                  class="w-4 h-4 transition-transform text-gray-400"
                  :class="{ 'rotate-180': isPostsDropdownOpen }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <div v-if="isPostsDropdownOpen" class="ml-8 mt-1 space-y-1">
                <SidebarLink
                  :href="route('roles.index')"
                  :active="route().current('roles.index')"
                  class="text-sm"
                >
                  All Roles
                </SidebarLink>
                <SidebarLink
                  :href="route('roles.create')"
                  :active="route().current('roles.create')"
                  class="text-sm"
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
      class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 transition-colors"
    >
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <span class="text-sm font-medium">Products</span>
      </div>
      <svg
        class="w-4 h-4 transition-transform text-gray-400"
        :class="{ 'rotate-180': isProductsDropdownOpen }"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <div v-if="isProductsDropdownOpen" class="ml-8 mt-1 space-y-1">
      <!-- Categories Section -->
      <div class="pl-2">

        <SidebarLink
          :href="route('categories.index')"
          :active="route().current('categories.index')"
          class="text-sm"
        >
          All Categories
        </SidebarLink>
      </div>

      <!-- Products Section -->
      <div class="pl-2 pt-1">

        <SidebarLink
          :href="route('products.index')"
          :active="route().current('products.index')"
          class="text-sm"
        >
          All Products
        </SidebarLink>

      </div>
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
          class="flex items-center gap-2 w-full p-2 rounded-lg hover:bg-gray-100 text-gray-700 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
      <header class="sticky top-0 z-20 bg-white border-b shadow-sm">
        <div class="flex items-center justify-between h-16 px-4">
          <button
            @click="toggleSidebar"
            class="p-2 rounded-lg hover:bg-gray-100 lg:hidden"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <div class="flex items-between gap-4">
            <!-- User profile or other header content -->
             <Link
             :href="route('activity.index')"
             class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
             :class="{ 'bg-gray-100 ': route().current('activity.index') }"
             >Activities</Link>
             <Link
             :href="route('profile.edit')"
             class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
             :class="{ 'bg-gray-100 ': route().current('profile.edit') }"
             >
              Profile
            </Link>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
        <div class="max-w-7xl mx-auto">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>
