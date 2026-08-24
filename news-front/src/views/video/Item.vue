<template>
  <div class="bg-[#1c1c1c] max-w-7xl mx-auto px-4 py-6">
    <!-- Header -->
    <h2 class="text-red-600 text-3xl font-bold mb-3 mt-14">ভিডিও</h2>

     <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-pulse">
      <div class="md:row-span-2 h-64 md:h-full bg-gray-800 rounded"></div>
      <div v-for="n in 4" :key="n" class="h-40 bg-gray-800 rounded"></div>
    </div>

    <!-- Empty -->
    <p v-else-if="videos.length === 0" class="text-gray-400 text-sm py-10">
      কোনো ভিডিও পাওয়া যায়নি।
    </p>

    <!-- Grid, chunked into blocks of 5 (1 featured + 4 small) -->
    <div v-else class="space-y-8">
      <div
        v-for="(block, blockIdx) in blocks"
        :key="blockIdx"
        class="grid grid-cols-1 md:grid-cols-3 gap-4"
      >
        <!-- Featured (first item of the block) -->
        <router-link
          v-if="block.featured"
          :to="{ name: 'video.show', params: { slug: block.featured.slug || block.featured.id } }"
          class="group md:row-span-2 flex flex-col"
        >
          <div class="relative w-full aspect-video md:aspect-auto md:h-full min-h-[280px] bg-gray-800 rounded overflow-hidden">
            <img
              :src="block.featured.thumbnail || ''"
              :alt="block.featured.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
            />
            <span class="absolute top-3 left-3 w-9 h-9 rounded-full bg-red-600 flex items-center justify-center">
              <i class="bi bi-play-fill text-white text-lg"></i>
            </span>
          </div>
          <h3 class="mt-3 text-white text-lg font-bold leading-snug group-hover:text-red-500 transition-colors">
            {{ block.featured.title }}
          </h3>
          <p class="mt-1 text-xs text-gray-400">{{ timeAgo(block.featured.created_at) }}</p>
        </router-link>

        <!-- Small cards (remaining items of the block) -->
        <router-link
          v-for="item in block.small"
          :key="item.id"
          :to="{ name: 'video.show', params: { slug: item.slug || item.id } }"
          class="group flex flex-col"
        >
          <div class="relative w-full aspect-video bg-gray-800 rounded overflow-hidden">
            <img
              :src="item.thumbnail || ''"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
            />
            <span class="absolute top-2 left-2 w-7 h-7 rounded-full bg-red-600 flex items-center justify-center">
              <i class="bi bi-play-fill text-white text-sm"></i>
            </span>
          </div>
          <h3 class="mt-2 text-white text-sm font-semibold leading-5 line-clamp-2 group-hover:text-red-500 transition-colors">
            {{ item.title }}
          </h3>
          <p class="mt-1 text-xs text-gray-400">{{ timeAgo(item.created_at) }}</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import api from "@/services/api";

interface Video {
  id: number;
  title: string;
  slug?: string;
  thumbnail: string | null;
  video_type?: "upload" | "embed";
  video_url?: string | null;
  description?: string | null;
  is_active?: boolean | number | string;
  created_at?: string;
}

const videos = ref<Video[]>([]);
const loading = ref(true);

const fetchVideos = async () => {
  loading.value = true;
  try {
    const response = await api.get("/video", { params: { limit: 10 } });

    let data = response?.data;
    if (data?.data) data = data.data;
    if (data?.data) data = data.data;

    let list: Video[] = Array.isArray(data) ? data : [];

    list = list.filter((v) => {
      if (v.is_active === undefined || v.is_active === null) return true;
      return Boolean(Number(v.is_active) || v.is_active === true || v.is_active === "true");
    });

    videos.value = list.slice(0, 10);
  } catch (error) {
    console.error("Video fetch error:", error);
    videos.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(fetchVideos);

// Split into blocks of 5: [featured, ...4 small]
const blocks = computed(() => {
  const chunks: { featured: Video | null; small: Video[] }[] = [];
  for (let i = 0; i < videos.value.length; i += 5) {
    const chunk = videos.value.slice(i, i + 5);
    chunks.push({
      featured: chunk[0] || null,
      small: chunk.slice(1),
    });
  }
  return chunks;
});

const toBengaliNumber = (num: number | string) => {
    const bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return num.toString().replace(/\d/g, (digit) => bengaliDigits[parseInt(digit)] ?? digit);
};

const parseUtcDate = (dateStr: string): Date => {
    const iso = dateStr.includes('T') ? dateStr : dateStr.replace(' ', 'T') + 'Z';
    return new Date(iso);
};

const timeAgo = (dateStr?: string | null) => {
    if (!dateStr) return '';
    const diffMs = Date.now() - parseUtcDate(dateStr).getTime();
    const mins = Math.floor(diffMs / 60000);
    if (mins < 1) return 'এইমাত্র';
    if (mins < 60) return `${toBengaliNumber(mins)} মিনিট আগে`;
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${toBengaliNumber(hours)} ঘণ্টা আগে`;
    const days = Math.floor(hours / 24);
    return `${toBengaliNumber(days)} দিন আগে`;
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>