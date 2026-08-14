<template>
  <header class="w-full bg-white shadow-sm">

    <!-- =========================
         DESKTOP TOP BAR
    ========================== -->
    <div class="hidden lg:grid max-w-7xl mx-auto grid-cols-2 items-center gap-6 py-4 px-4 border-b border-gray-200">
      <!-- Left Side: Date + Logo -->
      <div class="grid grid-cols-2 items-center">

        <!-- Date -->
        <div class="flex items-center space-x-2 text-base text-gray-600 font-medium">
          <i class="bi bi-calendar3"></i>
          <span>{{ formattedDate }}</span>
        </div>

        <!-- Logo -->
        <div class="flex justify-center">
          <router-link to="/" class="shrink-0">
            <img src="/images/khobor logo.png" alt="Khobor Logo" class="h-24 w-auto object-contain" />
          </router-link>
        </div>

      </div>


      <!-- Right Side: Media 1 + Media 2 -->
      <div class="flex items-center justify-end gap-6">

        <!-- Media 1 -->
        <a href="#" class="flex items-center gap-3 group min-w-0 w-64">
          <!-- Image -->
          <div class="relative shrink-0">
            <img src="/images/media.jpg" alt="Media" class="w-24 h-16 object-cover rounded" />

            <!-- Play Icon -->
            <div class="absolute inset-0 flex items-center justify-center">
              <i class="bi bi-play-circle-fill text-red-500 text-2xl drop-shadow"></i>
            </div>
          </div>

          <!-- Title -->
          <p class="text-sm font-medium text-gray-700 leading-5
               group-hover:text-red-600 transition-colors">
            বিদ্যুৎ সাশ্রয়ে দেশের সব বিপণিবিতান
          </p>
        </a>


        <!-- Media 2 -->
        <a href="#" class="flex items-center gap-3 group min-w-0 w-64">
          <!-- Image -->
          <div class="relative shrink-0">
            <img src="/images/media.jpg" alt="Media" class="w-24 h-16 object-cover rounded" />

            <!-- Play Icon -->
            <div class="absolute inset-0 flex items-center justify-center">
              <i class="bi bi-play-circle-fill text-red-500 text-2xl drop-shadow"></i>
            </div>
          </div>

          <!-- Title -->
          <p class="text-sm font-medium text-gray-700 leading-5
               group-hover:text-red-600 transition-colors">
            বিদ্যুৎ সাশ্রয়ে দেশের সব বিপণিবিতান,
            মার্কেট ও দোকানপাট খোলার
          </p>
        </a>

      </div>
    </div>

    <!-- =========================
         DESKTOP FIXED SPACER
    ========================== -->
    <div v-if="isDesktopSticky" class="hidden lg:block h-14"></div>


    <!-- =========================
         NAVIGATION
    ========================== -->
    <nav class="bg-white border-b border-gray-200 z-50
             transition-transform duration-300 ease-in-out" :class="navClasses">

      <!-- NAV CONTAINER -->
      <div class="max-w-7xl mx-auto px-4 h-14
               flex items-center justify-between">

        <!-- =========================
             MOBILE LOGO
        ========================== -->
        <a href="/" class="lg:hidden shrink-0">
          <img src="/images/khobor logo.png" alt="Khobor Logo" class="h-9 w-auto" />
        </a>


        <!-- =========================
             DESKTOP MENU
        ========================== -->
        <div class="hidden lg:flex items-center space-x-6
                 text-lg font-medium text-gray-800">
          <a href="#" class="hover:text-red-600 transition-colors">
            সর্বশেষ
          </a>

          <a href="#" class="hover:text-red-600 transition-colors">
            বাংলাদেশ
          </a>

          <a href="#" class="hover:text-red-600 transition-colors">
            রাজনীতি
          </a>

          <a href="#" class="hover:text-red-600 transition-colors">
            বিশ্ব
          </a>

          <a href="#" class="hover:text-red-600 transition-colors">
            বাণিজ্য
          </a>

          <a href="#" class="hover:text-red-600 transition-colors">
            মতামত
          </a>

          <a href="#" class="hover:text-red-600 transition-colors">
            খেলা
          </a>

          <a href="#" class="hover:text-red-600 transition-colors">
            বিনোদন
          </a>
        </div>


        <!-- DESKTOP RIGHT -->
        <div class="hidden lg:flex items-center space-x-4 ml-auto text-base font-medium">
          <button class="flex items-center gap-1 hover:text-red-600">
            <i class="bi bi-search"></i>
            <span>খুঁজুন</span>
          </button>

          <!-- Clean User Account Dropdown -->
          <UserAccount />
        </div>


        <!-- =========================
             MOBILE RIGHT
        ========================== -->
        <div class="flex lg:hidden items-center gap-4">

          <!-- Search -->
          <button type="button" class="text-gray-700 hover:text-red-600">
            <i class="bi bi-search text-xl"></i>
          </button>


          <!-- Login -->
          <UserAccount />


          <!-- Hamburger -->
          <button type="button" class="text-gray-700 hover:text-red-600" @click="mobileMenuOpen = !mobileMenuOpen">
            <i v-if="!mobileMenuOpen" class="bi bi-list text-3xl"></i>

            <i v-else class="bi bi-x-lg text-2xl"></i>
          </button>

        </div>

      </div>


      <!-- =========================
           MOBILE MENU
      ========================== -->
      <!-- =========================
     MOBILE MENU
========================== -->
<Transition
  enter-active-class="transition-all duration-300 ease-out"
  enter-from-class="opacity-0 -translate-y-4"
  enter-to-class="opacity-100 translate-y-0"
  leave-active-class="transition-all duration-250 ease-in"
  leave-from-class="opacity-100 translate-y-0"
  leave-to-class="opacity-0 -translate-y-4"
>
  <div
    v-if="mobileMenuOpen"
    class="lg:hidden bg-white border-t border-gray-200 shadow-md overflow-hidden"
  >
    <div class="max-w-7xl mx-auto px-4 py-3">

      <nav class="flex flex-col">

        <a
          href="#"
          class="py-3 border-b border-gray-100
                 text-gray-800 font-medium
                 hover:text-red-600 transition-colors"
          @click="mobileMenuOpen = false"
        >
          সর্বশেষ
        </a>

        <a
          href="#"
          class="py-3 border-b border-gray-100
                 text-gray-800 font-medium
                 hover:text-red-600 transition-colors"
          @click="mobileMenuOpen = false"
        >
          বাংলাদেশ
        </a>

        <a
          href="#"
          class="py-3 border-b border-gray-100
                 text-gray-800 font-medium
                 hover:text-red-600 transition-colors"
          @click="mobileMenuOpen = false"
        >
          রাজনীতি
        </a>

        <a
          href="#"
          class="py-3 border-b border-gray-100
                 text-gray-800 font-medium
                 hover:text-red-600 transition-colors"
          @click="mobileMenuOpen = false"
        >
          বিশ্ব
        </a>

        <a
          href="#"
          class="py-3 border-b border-gray-100
                 text-gray-800 font-medium
                 hover:text-red-600 transition-colors"
          @click="mobileMenuOpen = false"
        >
          বাণিজ্য
        </a>

        <a
          href="#"
          class="py-3 border-b border-gray-100
                 text-gray-800 font-medium
                 hover:text-red-600 transition-colors"
          @click="mobileMenuOpen = false"
        >
          মতামত
        </a>

        <a
          href="#"
          class="py-3 border-b border-gray-100
                 text-gray-800 font-medium
                 hover:text-red-600 transition-colors"
          @click="mobileMenuOpen = false"
        >
          খেলা
        </a>

        <a
          href="#"
          class="py-3 text-gray-800 font-medium
                 hover:text-red-600 transition-colors"
          @click="mobileMenuOpen = false"
        >
          বিনোদন
        </a>

      </nav>

    </div>
  </div>
</Transition>

    </nav>

  </header>
</template>


<script setup lang="ts">
import UserAccount from "@/components/UserAccount.vue";
import {ref, computed, onMounted, onUnmounted} from "vue";


/* =========================
   STATE
========================= */

const isMenuVisible = ref(true);

const isDesktopSticky = ref(false);

const lastScrollY = ref(0);

const mobileMenuOpen = ref(false);


/* =========================
   DATE
========================= */

const formattedDate = computed(() => {
  return new Date().toLocaleDateString("bn-BD", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});


/* =========================
   NAV CLASSES
========================= */

const navClasses = computed(() => {

  if (typeof window === "undefined") {
    return "";
  }

  /* Desktop */
  if (window.innerWidth >= 1024) {

    return isDesktopSticky.value
      ? "fixed top-0 left-0 right-0 shadow-md"
      : "relative";

  }


  /* Mobile */

  return [
    "fixed top-0 left-0 right-0 shadow-sm",
    isMenuVisible.value
      ? "translate-y-0"
      : "-translate-y-full"
  ];

});


/* =========================
   SCROLL
========================= */

const handleScroll = () => {

  const current = window.scrollY;


  /* Desktop */

  if (window.innerWidth >= 1024) {

    // Top bar height
    isDesktopSticky.value = current > 110;

    // Desktop menu always visible
    isMenuVisible.value = true;

    // Close mobile menu
    mobileMenuOpen.value = false;

  }


  /* Mobile */

  else {

    if (current <= 20) {

      isMenuVisible.value = true;

    }

    else if (current > lastScrollY.value) {

      // Scrolling DOWN
      isMenuVisible.value = false;

      // Close mobile menu
      mobileMenuOpen.value = false;

    }

    else {

      // Scrolling UP
      isMenuVisible.value = true;

    }

  }


  lastScrollY.value = current;

};


/* =========================
   RESIZE
========================= */

const handleResize = () => {

  handleScroll();

};


/* =========================
   MOUNT
========================= */

onMounted(() => {

  handleScroll();

  window.addEventListener(
    "scroll",
    handleScroll,
    {
      passive: true
    }
  );

  window.addEventListener(
    "resize",
    handleResize
  );

});


/* =========================
   UNMOUNT
========================= */

onUnmounted(() => {

  window.removeEventListener(
    "scroll",
    handleScroll
  );

  window.removeEventListener(
    "resize",
    handleResize
  );

});
</script>