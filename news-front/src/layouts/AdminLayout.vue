<!-- layouts/AdminLayout.vue -->
<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
      <div class="h-16 flex items-center justify-center border-b border-gray-700">
        <h2 class="text-xl font-bold">সংবাদ অ্যাডমিন প্যানেল</h2>
      </div>

      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <router-link
          to="/admin/dashboard"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-speedometer2 mr-2"></i>Dashboard
        </router-link>

        <!-- Category Menu -->
        <div>
          <button
            @click="isCategoryOpen = !isCategoryOpen"
            class="w-full flex items-center justify-between px-4 py-2 rounded hover:bg-gray-700 transition-colors"
            :class="{ 'text-emerald-400 font-semibold': isCategoryRouteActive }"
          >
            <span class="flex items-center gap-2">
              <i class="bi bi-folder2-open"></i>
              Category
            </span>
            <i class="bi" :class="isCategoryOpen ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
          </button>

          <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-40"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-40"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-show="isCategoryOpen" class="ml-6 mt-1 space-y-1 overflow-hidden">
              <router-link
                to="/admin/category"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                All Categories
              </router-link>
              <router-link
                to="/admin/sub_category"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Sub Category
              </router-link>
            </div>
          </Transition>
        </div>

        <!-- Article Menu -->
        <div>
          <button
            @click="isArticleOpen = !isArticleOpen"
            class="w-full flex items-center justify-between px-4 py-2 rounded hover:bg-gray-700 transition-colors"
            :class="{ 'text-emerald-400 font-semibold': isArticleRouteActive }"
          >
            <span class="flex items-center gap-2">
              <i class="bi bi-newspaper"></i>
              Articles
            </span>
            <i class="bi" :class="isArticleOpen ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
          </button>

          <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-96"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-96"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-show="isArticleOpen" class="ml-6 mt-1 space-y-1 overflow-hidden">
              <router-link
                to="/admin/article"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                All Articles
              </router-link>
              <router-link
                to="/admin/article/create"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Write New
              </router-link>
              <router-link
                to="/admin/article_pending"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Pending Review
              </router-link>
              <router-link
                to="/admin/tags"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Tags
              </router-link>
            </div>
          </Transition>
        </div>

        <router-link
          to="/admin/comments"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-chat-dots mr-2"></i>Comments
        </router-link>

        <router-link
          to="/admin/user"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-people mr-2"></i>Users
        </router-link>

        <router-link
          to="/admin/logo"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-image mr-2"></i>Logos
        </router-link>
      </nav>
    </aside>

    <!-- Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-white shadow h-16 px-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold">নিউজ অ্যাডমিন</h1>

        <!-- Profile -->
        <div class="relative" ref="accountMenuRef">
          <button
            @click.stop="isAccountsOpen = !isAccountsOpen"
            class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg"
          >
            <img
              v-if="userProfileImage && !imageError"
              :src="userProfileImage"
              @error="handleImageError"
              class="w-9 h-9 rounded-full object-cover border"
            >
            <i v-else class="bi bi-person-circle text-3xl"></i>

            <span class="font-medium">{{ user?.name }}</span>
            <i class="bi bi-chevron-down text-xs"></i>
          </button>

          <!-- Dropdown -->
          <Transition
            enter-active-class="transition duration-150"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="isAccountsOpen"
              class="absolute right-0 mt-2 w-60 bg-white rounded-lg shadow-lg border overflow-hidden z-50"
            >
              <div v-if="user" class="px-4 py-3 border-b">
                <p class="font-semibold">{{ user.name }}</p>
                <p class="text-xs text-gray-500">{{ user.email }}</p>
              </div>

              <router-link to="/admin/profile" class="block px-4 py-2 hover:bg-gray-100">
                <i class="bi bi-person mr-2"></i>Profile
              </router-link>

              <button
                @click="logout"
                :disabled="loggingOut"
                class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 disabled:opacity-50"
              >
                <i class="bi bi-box-arrow-right mr-2"></i>
                {{ loggingOut ? 'লগ আউট হচ্ছে...' : 'Logout' }}
              </button>
            </div>
          </Transition>
        </div>
      </header>

      <!-- Page -->
      <main class="flex-1 p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

interface AdminUser {
  id: number
  name: string
  email: string
  avatar: string | null
  role: string
}

const router = useRouter()
const route = useRoute()

const isAccountsOpen = ref(false)
const isCategoryOpen = ref(false)
const isArticleOpen = ref(false)
const imageError = ref(false)
const loggingOut = ref(false)
const accountMenuRef = ref<HTMLElement | null>(null)

const user = ref<AdminUser | null>(null)

// Load cached user immediately (fast paint), then refresh from server
const cached = localStorage.getItem('user')
if (cached) {
  try {
    user.value = JSON.parse(cached)
  } catch {
    user.value = null
  }
}

const userProfileImage = computed(() => user.value?.avatar || null)

const handleImageError = () => {
  imageError.value = true
}

// Highlight parent menu button when a child route is active
const isCategoryRouteActive = computed(() =>
  route.path.startsWith('/admin/category') || route.path.startsWith('/admin/sub_category')
)

const isArticleRouteActive = computed(() =>
  route.path.startsWith('/admin/article') ||
  route.path.startsWith('/admin/tags') ||
  route.path.startsWith('/admin/article_pending')
)

// Auto-expand the relevant menu section on load / route change
if (isCategoryRouteActive.value) isCategoryOpen.value = true
if (isArticleRouteActive.value) isArticleOpen.value = true

const logout = async () => {
  loggingOut.value = true
  try {
    await api.post('/logout')
  } catch (e) {
    // even if the server call fails, clear local session so the user isn't stuck
    console.error('Logout request failed:', e)
  } finally {
    localStorage.removeItem('apiToken')
    localStorage.removeItem('user')
    loggingOut.value = false
    isAccountsOpen.value = false
    router.push({ name: 'login' })
  }
}

// Close the account dropdown when clicking outside it
const handleClickOutside = (event: MouseEvent) => {
  if (accountMenuRef.value && !accountMenuRef.value.contains(event.target as Node)) {
    isAccountsOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)

  // Refresh user in background in case the cache is stale
  api.get('/me').then(({ data }) => {
    user.value = data.user
    localStorage.setItem('user', JSON.stringify(data.user))
  }).catch(() => {
    // router guard already handles redirect on 401; ignore here
  })
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>