<template>
  <div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="text-gray-500 animate-pulse font-medium">ভিডিও লোড হচ্ছে...</div>
    </div>

    <div v-else-if="video" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- LEFT SECTION: Main Video Player & Details -->
      <div class="lg:col-span-2 space-y-4">
        
        <!-- Video Player Wrapper -->
        <div class="relative w-full aspect-video bg-black rounded-lg overflow-hidden shadow-md">
          <!-- Embed Video (YouTube) -->
          <iframe
            v-if="video.video_type === 'embed' || isYouTubeUrl(video.video_url)"
            :src="getEmbedUrl(video.video_url)"
            class="w-full h-full border-0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>

          <!-- Direct Uploaded Video -->
          <video
            v-else-if="video.video_url"
            controls
            autoplay
            class="w-full h-full object-contain"
            :poster="video.thumbnail || ''"
          >
            <source :src="video.video_url" type="video/mp4" />
            আপনার ব্রাউজার ভিডিও প্লেয়ার সাপোর্ট করে না।
          </video>

          <div v-else class="flex items-center justify-center h-full text-white">
            ভিডিও টি পাওয়া যায়নি।
          </div>
        </div>

        <!-- Video Title -->
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-snug">
          {{ video.title }}
        </h1>

        <!-- Action Bar: Social Icons & Date -->
        <div class="flex flex-wrap items-center justify-between gap-4 py-2 border-b border-gray-100">
          <div class="flex items-center gap-2">
            <!-- Share Buttons -->
            <button class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:opacity-90">
              <i class="bi bi-facebook text-sm"></i>
            </button>
            <button class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center hover:opacity-90">
              <i class="bi bi-x text-base"></i>
            </button>
            <button class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200">
              <i class="bi bi-share-fill text-sm"></i>
            </button>
            <button class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200">
              <i class="bi bi-bookmark text-sm"></i>
            </button>
            <!-- YouTube Subscribe Badge -->
            <span class="inline-flex items-center gap-1 bg-red-600 text-white text-xs px-2.5 py-1.5 rounded font-medium ml-2">
              <i class="bi bi-youtube"></i> YouTube
            </span>
          </div>

          <!-- Publish Date -->
          <span class="text-xs text-gray-500">
            প্রকাশ: {{ formatDate(video.created_at) }}
          </span>
        </div>

        <!-- Video Description -->
        <div class="text-gray-800 text-base leading-relaxed pt-2 whitespace-pre-line">
          {{ video.description || 'কোনো বিবরণ নেই।' }}
        </div>
      </div>

      <!-- RIGHT SECTION: Sidebar Ad & Up Next Videos -->
      <div class="space-y-6">
        
        <!-- Ad Banner Placeholder -->
        <div class="bg-gray-50 border border-gray-200 rounded p-4 text-center">
          <span class="text-xs text-gray-400 block mb-2">বিজ্ঞাপন</span>
          <div class="w-full bg-gray-200 h-60 flex items-center justify-center text-gray-500 text-sm font-medium rounded">
            [ Ad Banner Space ]
          </div>
        </div>

        <!-- Sidebar Header: "পরবর্তী ভিডিও" -->
        <div>
          <h2 class="text-xl font-bold text-gray-900 mb-4 pb-1 border-b-2 border-red-600 inline-block">
            পরবর্তী ভিডিও
          </h2>

          <!-- Up Next Video List -->
          <div class="space-y-4">
            <router-link
              v-for="item in sideVideos"
              :key="item.id"
              :to="{ name: 'video.show', params: { slug: item.slug || item.id } }"
              class="flex gap-3 group items-start"
            >
              <!-- Info Title & Date -->
              <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-800 group-hover:text-red-600 transition-colors line-clamp-2 leading-tight">
                  {{ item.title }}
                </h3>
                <span class="text-xs text-gray-400 mt-2 block">
                  {{ formatDate(item.created_at) }}
                </span>
              </div>

              <!-- Thumbnail -->
              <div class="relative shrink-0 w-28 h-20 bg-gray-100 rounded overflow-hidden">
                <img
                  :src="item.thumbnail || '/images/media.jpg'"
                  :alt="item.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                  @error="handleImageError"
                />
                <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                  <i class="bi bi-play-circle-fill text-red-600 text-xl drop-shadow"></i>
                </div>
              </div>
            </router-link>
          </div>
        </div>

      </div>
    </div>

    <!-- Fallback if Not Found -->
    <div v-else class="text-center py-12 text-gray-500">
      ভিডিও পাওয়া যায়নি।
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';

interface Video {
  id: number;
  title: string;
  slug: string;
  thumbnail: string | null;
  video_type: 'upload' | 'embed';
  video_url: string | null;
  description: string | null;
  is_active?: boolean | number;
  created_at?: string;
}

const route = useRoute();
const video = ref<Video | null>(null);
const sideVideos = ref<Video[]>([]);
const loading = ref(true);

// Fetch current video detail using the slug parameter
const fetchVideoDetail = async (slugOrId: string) => {
  loading.value = true;
  try {
    const res = await api.get(`/video/${slugOrId}`);
    let data = res?.data;
    if (data && typeof data === 'object' && 'data' in data) {
      data = data.data;
    }
    video.value = data;
  } catch (err) {
    console.error('Error fetching video detail:', err);
    video.value = null;
  } finally {
    loading.value = false;
  }
};

// Fetch list of videos for the sidebar ("পরবর্তী ভিডিও")
const fetchSideVideos = async () => {
  try {
    const res = await api.get('/video');
    let data = res?.data;
    if (data && typeof data === 'object' && 'data' in data) {
      data = data.data;
    }
    if (Array.isArray(data)) {
      const currentSlug = route.params.slug as string;
      sideVideos.value = data.filter(v => v.slug !== currentSlug && String(v.id) !== currentSlug);
    }
  } catch (err) {
    console.error('Error fetching side videos:', err);
  }
};

// YouTube helper check
const isYouTubeUrl = (url?: string | null) => {
  if (!url) return false;
  return url.includes('youtube.com') || url.includes('youtu.be');
};

// Formats YouTube links to work cleanly inside an iframe
const getEmbedUrl = (url?: string | null) => {
  if (!url) return '';
  if (url.includes('youtube.com/embed/')) return url;
  
  let videoId = '';
  if (url.includes('youtu.be/')) {
    videoId = url.split('youtu.be/')[1]?.split('?')[0];
  } else if (url.includes('watch?v=')) {
    videoId = url.split('watch?v=')[1]?.split('&')[0];
  }
  return videoId ? `https://www.youtube.com/embed/${videoId}?autoplay=1` : url;
};

// Bengali Date Formatter
const formatDate = (dateString?: string) => {
  if (!dateString) return 'কিছুক্ষণ আগে';
  try {
    return new Date(dateString).toLocaleDateString('bn-BD', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  } catch {
    return dateString;
  }
};

const handleImageError = (e: Event) => {
  const img = e.target as HTMLImageElement;
  if (img.src !== '/images/media.jpg') {
    img.src = '/images/media.jpg';
  }
};

// Initial Fetch
onMounted(() => {
  const currentSlug = route.params.slug as string;
  if (currentSlug) {
    fetchVideoDetail(currentSlug);
    fetchSideVideos();
  }
});

// Watch route parameter change (allows clicking up-next videos seamlessly)
watch(
  () => route.params.slug,
  (newSlug) => {
    if (newSlug) {
      fetchVideoDetail(newSlug as string);
      fetchSideVideos();
    }
  }
);
</script>