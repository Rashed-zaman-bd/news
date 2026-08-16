<template>
  <div class="relative" ref="dropdownRef">
    <!-- Trigger Button -->
    <button
      type="button"
      @click="toggleDropdown"
      class="flex items-center gap-2 hover:text-red-600 transition-colors focus:outline-none py-1 px-2 rounded-md hover:bg-gray-100"
    >
      <!-- Logged In State -->
      <template v-if="isLoggedIn && user">
        <img
          v-if="user.avatar"
          :src="user.avatar"
          :alt="user.name"
          class="w-7 h-7 rounded-full object-cover border border-gray-300"
        />
        <i v-else class="bi bi-person-circle text-xl text-gray-700"></i>
        <span class="max-w-[120px] truncate text-sm font-semibold text-gray-800">
          {{ user.name }}
        </span>
      </template>

      <!-- Guest State -->
      <template v-else>
        <i class="bi bi-person-fill text-xl"></i>
        <span class="hidden sm:block text-base font-medium">একাউন্ট</span>
      </template>
    </button>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition ease-out duration-150"
      enter-from-class="transform opacity-0 scale-95 -translate-y-2"
      enter-to-class="transform opacity-100 scale-100 translate-y-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="transform opacity-100 scale-100 translate-y-0"
      leave-to-class="transform opacity-0 scale-95 -translate-y-2"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-100 py-2 z-50 text-left text-gray-700"
      >
        <!-- Logged In Menu -->
        <template v-if="isLoggedIn && user">
          <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/50">
            <p class="text-xs text-gray-500">লগইন করা আছে:</p>
            <p class="text-sm font-semibold text-gray-800 truncate">{{ user.name }}</p>
            <p class="text-xs text-gray-500 truncate" v-if="user.email">{{ user.email }}</p>
          </div>

          <router-link
            to="/profile"
            @click="isOpen = false"
            class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-gray-50 hover:text-red-600 transition-colors"
          >
            <i class="bi bi-person-gear text-base text-gray-500"></i>
            <span>প্রোফাইল আপডেট</span>
          </router-link>

          <div class="my-1 border-t border-gray-100"></div>

          <button
            type="button"
            @click="handleLogout"
            class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left"
          >
            <i class="bi bi-box-arrow-right text-base"></i>
            <span>লগআউট</span>
          </button>
        </template>

        <!-- Guest Menu -->
        <template v-else>
          <router-link
            to="/login"
            @click="isOpen = false"
            class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-gray-50 hover:text-red-600 transition-colors"
          >
            <i class="bi bi-box-arrow-in-right text-base text-gray-500"></i>
            <span>লগইন (Login)</span>
          </router-link>

          <router-link
            to="/register"
            @click="isOpen = false"
            class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-gray-50 hover:text-red-600 transition-colors"
          >
            <i class="bi bi-person-plus text-base text-gray-500"></i>
            <span>রেজিস্টার (Register)</span>
          </router-link>
        </template>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const isOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const token = ref<string | null>(localStorage.getItem('apiToken'));
const userRaw = ref<string | null>(localStorage.getItem('user'));

const isLoggedIn = computed(() => !!token.value);

const user = computed(() => {
  if (!userRaw.value) return null;
  try {
    return JSON.parse(userRaw.value);
  } catch (e) {
    return null;
  }
});

// Sync local ref state with localStorage
const syncAuthState = () => {
  token.value = localStorage.getItem('apiToken');
  userRaw.value = localStorage.getItem('user');
};

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    isOpen.value = false;
  }
};

const handleLogout = async () => {
  isOpen.value = false;

  try {
    await api.post('/logout').catch(() => {});
  } finally {
    localStorage.removeItem('apiToken');
    localStorage.removeItem('user');
    
    // Notify all listeners that auth state changed
    window.dispatchEvent(new Event('auth-changed'));

    Swal.fire({
      icon: 'success',
      title: 'লগআউট সফল হয়েছে',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 1500,
      background: '#16a34a',
      color: '#ffffff'
    });

    router.push('/');
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  // Listen for custom auth events (login/register/logout)
  window.addEventListener('auth-changed', syncAuthState);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  window.removeEventListener('auth-changed', syncAuthState);
});
</script>