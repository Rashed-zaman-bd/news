<template>
  <header class="w-full bg-white shadow-sm">

    <!-- =========================
         DESKTOP TOP BAR
    ========================== -->
    <div class="hidden lg:grid max-w-7xl mx-auto grid-cols-2 items-center gap-6 py-6 px-4 border-b border-gray-200">
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
            <img 
              v-if="logo?.text_logo"
              :src="logo.text_logo"
              :alt="logo.title || 'Khobor Logo'"
              class="h-20 w-auto object-contain">
          </router-link>
        </div>
      </div>

      <!-- Right Side: Media Top Items -->
      <NavbarVideo/>
    </div>

    <!-- DESKTOP FIXED SPACER -->
    <div v-if="isDesktopSticky" class="hidden lg:block h-14"></div>

    <!-- =========================
         NAVIGATION
    ========================== -->
    <nav class="bg-white border-b border-gray-200 z-50 transition-transform duration-300 ease-in-out" :class="navClasses">
      <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">

        <!-- MOBILE LOGO -->
        <div class="lg:hidden flex flex-col items-start shrink-0">
          <router-link to="/" class="shrink-0">
            <img 
              v-if="logo?.text_logo"
              :src="logo.text_logo"
              :alt="logo.title || 'Khobor Logo'"
              class="h-9 w-auto object-contain">
          </router-link>
          <span class="text-xs text-gray-600 mt-1">{{ formattedDate }}</span>
        </div>

        <!-- =========================
             DYNAMIC DESKTOP MENU BAR
        ========================== -->
        <div class="hidden lg:flex items-center space-x-10 text-lg  text-gray-800">

          <div class="flex items-center gap-1 cursor-pointer hover:text-red-600 transition-colors">
              <router-link :to="`/category/latest`" class="whitespace-nowrap">
                সর্বশেষ
              </router-link>
          </div>

          <!-- Skeleton Loader -->
          <template v-if="categoriesLoading">
            <div v-for="i in 6" :key="i" class="h-5 w-16 bg-gray-200 animate-pulse rounded"></div>
          </template>

          <!-- Dynamic Dynamic Categories -->
          <template v-else>
            <div 
              v-for="cat in categories" 
              :key="cat.id" 
              class="relative group py-4"
            >
              <div class="flex items-center gap-1 cursor-pointer hover:text-red-600 transition-colors">
                <router-link :to="`/category/${cat.slug}`" class="whitespace-nowrap">
                  {{ cat.name }}
                </router-link>
              </div>
            </div>
          </template>

          <div class="flex items-center gap-1 cursor-pointer hover:text-red-600 transition-colors">
              <router-link :to="`/video/show`" class="whitespace-nowrap">
                ভিডিও
              </router-link>
          </div>
        </div>

        <!-- DESKTOP RIGHT -->
        <div class="hidden lg:flex items-center space-x-4 ml-auto text-lg ">
          <button class="flex items-center gap-1 hover:text-red-600">
            <i class="bi bi-search"></i>
            <span>খুঁজুন</span>
          </button>
          <UserAccount />
        </div>

        <!-- MOBILE RIGHT -->
        <div class="flex lg:hidden items-center gap-4">
          <button type="button" class="text-gray-700 hover:text-red-600">
            <i class="bi bi-search text-xl"></i>
          </button>
          <UserAccount />
          <button type="button" class="text-gray-700 hover:text-red-600" @click="mobileMenuOpen = !mobileMenuOpen">
            <i v-if="!mobileMenuOpen" class="bi bi-list text-3xl"></i>
            <i v-else class="bi bi-x-lg text-2xl"></i>
          </button>
        </div>

      </div>

      <!-- =========================
           DYNAMIC MOBILE MENU
      ========================== -->
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-250 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
      >
        <div v-if="mobileMenuOpen" class="lg:hidden bg-white border-t border-gray-200 shadow-md overflow-hidden">
          <div class="max-w-7xl mx-auto px-4 py-3">
            <nav class="flex flex-col">

              <div class="flex items-center gap-1 cursor-pointer hover:text-red-600 transition-colors">
                  <router-link :to="`/category/latest`" class="whitespace-nowrap"
                      @click="closeMobileMenu"
                  >
                    সর্বশেষ
                  </router-link>
              </div>

              <!-- Dynamic Categories -->
              <div v-for="cat in categories" :key="cat.id" class="border-b border-gray-100">
                <div class="flex items-center justify-between py-3">
                  <router-link
                    :to="`/category/${cat.slug}`"
                    class="text-gray-800 font-medium hover:text-red-600 transition-colors flex-1"
                    @click="closeMobileMenu"
                  >
                    {{ cat.name }}
                  </router-link>
                </div>
              </div>
            </nav>
          </div>
        </div>
      </Transition>
    </nav>
  </header>
</template>

<script setup lang="ts">
import UserAccount from "@/components/UserAccount.vue";
import { ref, computed, onMounted, onUnmounted } from "vue";
import api from "@/services/api";
import NavbarVideo from "@/components/NavbarVideo.vue";

interface NavCategory {
  id: number;
  name: string;
  slug: string;
  icon: string | null;
  active_children?: NavCategory[];
  children?: NavCategory[];
}

interface LogoData {
  id: number
  title: string
  text_logo: string | null
}

/* =========================
   STATE
========================= */
const isMenuVisible = ref(true);
const isDesktopSticky = ref(false);
const lastScrollY = ref(0);
const mobileMenuOpen = ref(false);

const categories = ref<NavCategory[]>([]);
const categoriesLoading = ref(true);
const expandedMobileCategory = ref<number | null>(null);

const logo = ref<LogoData | null>(null)

//Fetch logo
const fetchLogo = async () => {
  try{
    const {data} = await api.get('/logo')
    logo.value = data.data || data
  }catch(e){
    console.error('Failed to load site logo:',e)
  }
}

/* =========================
   FETCH CATEGORIES
========================= */
const fetchCategories = async () => {
  categoriesLoading.value = true;
  try {
    const { data } = await api.get("/category");
    categories.value = data.data;
  } catch (e) {

  } finally {
    categoriesLoading.value = false;
  }
};

const toggleMobileCategory = (id: number) => {
  expandedMobileCategory.value = expandedMobileCategory.value === id ? null : id;
};

const closeMobileMenu = () => {
  mobileMenuOpen.value = false;
  expandedMobileCategory.value = null;
};

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
  if (typeof window === "undefined") return "";

  if (window.innerWidth >= 1024) {
    return isDesktopSticky.value ? "fixed top-0 left-0 right-0 shadow-md" : "relative";
  }

  return [
    "fixed top-0 left-0 right-0 shadow-sm",
    isMenuVisible.value ? "translate-y-0" : "-translate-y-full",
  ];
});

/* =========================
   SCROLL & RESIZE
========================= */
const handleScroll = () => {
  const current = window.scrollY;

  if (window.innerWidth >= 1024) {
    isDesktopSticky.value = current > 110;
    isMenuVisible.value = true;
    mobileMenuOpen.value = false;
  } else {
    if (current <= 20) {
      isMenuVisible.value = true;
    } else if (current > lastScrollY.value) {
      isMenuVisible.value = false;
      mobileMenuOpen.value = false;
    } else {
      isMenuVisible.value = true;
    }
  }

  lastScrollY.value = current;
};

const handleResize = () => {
  handleScroll();
};

/* =========================
   LIFECYCLE
========================= */
onMounted(() => {

  handleScroll();
  fetchCategories();
  fetchLogo();

  window.addEventListener("scroll", handleScroll, { passive: true });
  window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
  window.removeEventListener("resize", handleResize);
});
</script>