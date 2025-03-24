<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import SidebarLink from "@/Components/SidebarLink.vue";

// Dropdown state
const isUsersDropdownOpen = ref(false);
const isPostsDropdownOpen = ref(false);

// Sidebar state
const isSidebarOpen = ref(false);

// Toggle dropdowns
const toggleUsersDropdown = () => {
  isUsersDropdownOpen.value = !isUsersDropdownOpen.value;
  isPostsDropdownOpen.value = false; // Close other dropdown
};

const togglePostsDropdown = () => {
  isPostsDropdownOpen.value = !isPostsDropdownOpen.value;
  isUsersDropdownOpen.value = false; // Close other dropdown
};

// Toggle sidebar
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
  <div class="w-full h-full">
    <!-- Sidebar Toggle Button (Mobile) -->
    <button
      @click="toggleSidebar"
      class="fixed z-20 top-4 left-4 p-2 bg-white rounded-lg shadow-lg lg:hidden"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16M4 18h16"
        />
      </svg>
    </button>

    <!-- Sidebar -->
    <aside
      class="fixed z-10 top-0 pb-3 px-6 w-64 flex flex-col justify-between h-screen border-r bg-white transition-transform duration-300 lg:translate-x-0"
      :class="{ '-translate-x-full': !isSidebarOpen }"
    >
      <div>
        <div class="-mx-6 px-6 py-4">
          Gestion de Stock
        </div>

        <ul class="space-y-2 tracking-wide mt-8">
          <!-- Dashboard Link -->
          <li>
            <SidebarLink href="/dashboard" :active="route().current('dashboard')">
              <svg
                class="-ml-1 h-6 w-6"
                viewBox="0 0 24 24"
                fill="none"
              >
                <path
                  d="M6 8a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V8ZM6 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-1Z"
                  class="fill-current text-cyan-400 dark:fill-slate-600"
                ></path>
                <path
                  d="M13 8a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2V8Z"
                  class="fill-current text-cyan-200 group-hover:text-cyan-300"
                ></path>
                <path
                  d="M13 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-1Z"
                  class="fill-current group-hover:text-sky-300"
                ></path>
              </svg>
              <span class="-mr-1 font-medium">Dashboard</span>
            </SidebarLink>
          </li>

          <!-- Users Dropdown -->
          <li>
            <div class="relative">
              <button
                @click="toggleUsersDropdown"
                class="w-full flex items-center justify-between p-2 text-gray-600 hover:bg-gray-100 rounded-md transition duration-300"
              >
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
                    />
                  </svg>
                  <span class="ml-2 font-medium">Users</span>
                </div>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 transition-transform"
                  :class="{ 'rotate-180': isUsersDropdownOpen }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </button>

              <!-- Dropdown Content -->
              <div
                v-if="isUsersDropdownOpen"
                class="mt-2 pl-8 space-y-2"
              >
                <SidebarLink
                  :href="route('users.index')"
                  :active="route().current('users.index')"
                >
                  <span class="font-medium">All Users</span>
                </SidebarLink>
                <SidebarLink
                  :href="route('users.create')"
                  :active="route().current('users.create')"
                >
                  <span class="font-medium">Create User</span>
                </SidebarLink>
              </div>
            </div>
          </li>

          <!-- Posts Dropdown -->
          <li>
            <div class="relative">
              <button
                @click="togglePostsDropdown"
                class="w-full flex items-center justify-between p-2 text-gray-600 hover:bg-gray-100 rounded-md transition duration-300"
              >
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"
                    />
                  </svg>
                  <span class="ml-2 font-medium">Posts</span>
                </div>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 transition-transform"
                  :class="{ 'rotate-180': isPostsDropdownOpen }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </button>

              <!-- Dropdown Content -->
              <div
                v-if="isPostsDropdownOpen"
                class="mt-2 pl-8 space-y-2"
              >
                <SidebarLink
                  :href="route('users.index')"
                  :active="route().current('users.index')"
                >
                  <span class="font-medium">All Posts</span>
                </SidebarLink>
                <SidebarLink
                  :href="route('users.create')"
                  :active="route().current('users.create')"
                >
                  <span class="font-medium">Create Post</span>
                </SidebarLink>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <!-- Logout Button -->
      <div class="px-6 -mx-6 pt-4 flex justify-between items-center border-t">
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="px-4 py-3 flex items-center space-x-4 rounded-md text-gray-600 group hover:text-red-400"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
            />
          </svg>
          <span class="group-hover:text-red-400">Logout</span>
        </Link>
      </div>
    </aside>

    <!-- Main Content -->
    <div
      class="ml-0 lg:ml-64 mb-6 transition-all duration-300"
      :class="{ 'lg:ml-0': !isSidebarOpen }"
    >
      <div class="sticky z-10 top-0 h-16 border-b bg-white lg:py-2.5">
        <div class="px-6 flex items-center justify-between space-x-4 2xl:container">
          <h5 hidden class="text-2xl text-gray-600 font-medium lg:block">
            Dashboard
          </h5>
          <button
            @click="toggleSidebar"
            class="w-12 h-16 -mr-2 border-r lg:hidden"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 my-auto"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
          <div class="flex space-x-4">
            <!-- Search Bar and Other Buttons -->
          </div>
        </div>
      </div>

      <div class="px-6 pt-6 2xl:container">
        <slot />
      </div>
    </div>
  </div>
</template>