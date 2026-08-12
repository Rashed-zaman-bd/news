<template>
  <header class="w-full bg-white shadow-sm">
    <!-- Desktop Top Bar -->
    <div
      class="hidden lg:flex container mx-auto items-center justify-between py-4 px-4 border-b border-gray-200"
    >
      <div class="flex items-center space-x-2 text-sm text-gray-600 font-medium">
        <i class="bi bi-calendar3"></i>
        <span>{{ formattedDate }}</span>
      </div>

      <a href="/" class="shrink-0">
        <img
          src="/images/khobor logo.png"
          alt="Khobor Logo"
          class="h-12 w-auto object-contain"
        />
      </a>

      <div class="flex items-center space-x-4">
        <a href="#">
          <img
            src="/images/media.jpg"
            class="w-36 h-24 object-cover rounded"
          />
        </a>

        <a href="#">
          <img
            src="/images/media.jpg"
            class="w-36 h-24 object-cover rounded"
          />
        </a>
      </div>
    </div>

    <!-- Spacer when desktop menu becomes fixed -->
    <div
      v-if="isDesktopSticky"
      class="hidden lg:block h-14"
    ></div>

    <!-- Navigation -->
    <nav
      class="bg-white border-b border-gray-200 z-50 transition-all duration-300 ease-in-out"
      :class="navClasses"
    >
      <div
        class="container mx-auto px-4 flex items-center justify-between h-14"
      >
        <!-- Mobile Logo -->
        <a href="/" class="lg:hidden">
          <img
            src="/images/khobor logo.png"
            class="h-8"
          />
        </a>

        <!-- Desktop Menu -->
        <div
          class="hidden lg:flex items-center space-x-6 text-sm font-medium text-gray-800"
        >
          <a href="#" class="hover:text-red-600">সর্বশেষ</a>
          <a href="#" class="hover:text-red-600">বাংলাদেশ</a>
          <a href="#" class="hover:text-red-600">রাজনীতি</a>
          <a href="#" class="hover:text-red-600">বিশ্ব</a>
          <a href="#" class="hover:text-red-600">বাণিজ্য</a>
          <a href="#" class="hover:text-red-600">মতামত</a>
          <a href="#" class="hover:text-red-600">খেলা</a>
          <a href="#" class="hover:text-red-600">বিনোদন</a>
        </div>

        <!-- Right -->
        <div class="flex items-center space-x-4 ml-auto text-sm">
          <button class="flex items-center gap-1 hover:text-red-600">
            <i class="bi bi-search"></i>
            <span>খুঁজুন</span>
          </button>

          <button class="flex items-center gap-1 hover:text-red-600">
            <i class="bi bi-person-fill"></i>
            <span>Login</span>
          </button>

          <button class="lg:hidden">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";

const isMenuVisible = ref(true);
const isDesktopSticky = ref(false);
const lastScrollY = ref(0);

const formattedDate = computed(() => {
  return new Date().toLocaleDateString("bn-BD", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});

const navClasses = computed(() => {
  // Desktop
  if (window.innerWidth >= 1024) {
    return isDesktopSticky.value
      ? "fixed top-0 left-0 right-0 shadow-md"
      : "relative";
  }

  // Mobile
  return [
    "fixed top-0 left-0 right-0",
    isMenuVisible.value ? "translate-y-0" : "-translate-y-full",
  ];
});

const handleScroll = () => {
  const current = window.scrollY;

  if (window.innerWidth >= 1024) {
    // Height of top bar
    isDesktopSticky.value = current > 110;
  } else {
    if (current <= 50) {
      isMenuVisible.value = true;
    } else if (current > lastScrollY.value) {
      isMenuVisible.value = false;
    } else {
      isMenuVisible.value = true;
    }
  }

  lastScrollY.value = current;
};

const handleResize = () => {
  handleScroll();
};

onMounted(() => {
  handleScroll();

  window.addEventListener("scroll", handleScroll, {
    passive: true,
  });

  window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
  window.removeEventListener("resize", handleResize);
});
</script>