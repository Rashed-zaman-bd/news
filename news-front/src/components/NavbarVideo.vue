<template>
  <div>
    <!-- Loading State -->
    <div
      v-if="loading"
      class="flex items-center justify-end gap-6 text-sm text-gray-500 animate-pulse"
    >
      Loading videos...
    </div>

    <!-- Videos List -->
    <div
      v-else-if="activeVideos.length > 0"
      class="flex items-center justify-end gap-2"
    >
      <router-link
        v-for="video in activeVideos"
        :key="video.id"
        :to="{
          name: 'video.show',
          params: { slug: video.slug || video.id }
        }"
        class="flex items-center gap-3 group min-w-0 w-64"
      >
        <!-- Thumbnail -->
        <div class="relative shrink-0 w-36 h-24 bg-gray-100 rounded overflow-hidden">
          <img
            :src="video.thumbnail || ''"
            :alt="video.title"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"

          />

          <!-- Play Icon -->
          <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition-colors">
            <i class="bi bi-play-circle-fill text-red-500 text-2xl drop-shadow"></i>
          </div>
        </div>

        <!-- Title -->
        <p class="text-base font-medium text-gray-700 leading-5 group-hover:text-red-600 transition-colors">
          {{ video.title }}
        </p>
      </router-link>
    </div>

    <!-- No videos fallback -->
    <p v-else class="text-sm text-gray-400">
      No videos available.
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import api from "@/services/api";
import { useRoute } from 'vue-router';

const route = useRoute();

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

const activeVideos = computed(() => {
  return videos.value.filter((v) => {
    if (v.is_active === undefined || v.is_active === null) return true;
    return Boolean(Number(v.is_active) || v.is_active === true || v.is_active === "true");
  });
});

const fetchVideos = async () => {
  loading.value = true;
  try {
    const response = await api.get("/video");
    
    // Deep extraction to handle Laravel Resource, Paginated, or Direct Arrays
    let dataPayload = response?.data;
    if (dataPayload?.data) {
      dataPayload = dataPayload.data;
    }
    if (dataPayload?.data) {
      dataPayload = dataPayload.data; // Handles nested paginated API wrappers
    }

    videos.value = Array.isArray(dataPayload) ? dataPayload : [];
  } catch (error) {
    console.error("API Fetch Error:", error);
    videos.value = [];
  } finally {
    loading.value = false;
  }
};


onMounted(fetchVideos);
</script>