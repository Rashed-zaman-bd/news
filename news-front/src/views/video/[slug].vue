<template>
    <div class="max-w-[1280px] mx-auto px-4 py-6 ">
        <div v-if="topAds[0]"
            class="w-full border-y border-gray-200 py-2 mt-10 mb-5 flex flex-col items-center cursor-pointer"
            @click="trackClick(topAds[0])">
            <img :src="topAds[0].image" :alt="topAds[0].name" class="w-full max-w-[728px] h-auto object-contain" />
            <span class="text-[10px] text-gray-400 mt-1">বিজ্ঞাপন — {{ topAds[0].provider }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8">

            <!-- ==================== MAIN COLUMN ==================== -->
            <div class="min-w-0">
                <!-- Loading -->
                <div v-if="loading" class="animate-pulse space-y-4">
                    <div class="w-full aspect-video bg-gray-200 rounded"></div>
                    <div class="h-7 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                </div>

                <!-- Not found -->
                <div v-else-if="!video" class="py-20 text-center text-gray-500">
                    ভিডিওটি খুঁজে পাওয়া যায়নি।
                </div>

                <template v-else>

                    <!-- Player -->
                    <div class="relative w-full aspect-video bg-black rounded overflow-hidden">
                        <iframe v-if="video.video_type === 'embed' && embedUrl" :src="embedUrl"
                            class="absolute inset-0 w-full h-full" title="video player"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen />
                        <video v-else-if="video.video_url" :src="video.video_url" :poster="video.thumbnail || undefined"
                            class="absolute inset-0 w-full h-full" controls playsinline />
                    </div>

                    <!-- Title -->
                    <h1 class="mt-4 text-2xl md:text-[28px] font-bold text-gray-900 leading-snug">
                        {{ video.title }}
                    </h1>

                    <!-- Share row -->
                    <div class="mt-3 flex items-center gap-3">
                        <a :href="shareLinks.facebook" target="_blank" rel="noopener"
                            class="w-8 h-8 rounded-full bg-[#1877F2] text-white flex items-center justify-center hover:opacity-90"
                            aria-label="ফেসবুকে শেয়ার করুন">
                            <i class="bi bi-facebook text-sm"></i>
                        </a>
                        <a :href="shareLinks.twitter" target="_blank" rel="noopener"
                            class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center hover:opacity-90"
                            aria-label="এক্স-এ শেয়ার করুন">
                            <i class="bi bi-twitter-x text-sm"></i>
                        </a>
                        <button type="button" @click="copyLink"
                            class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200"
                            aria-label="লিংক কপি করুন">
                            <i class="bi bi-share-fill text-sm"></i>
                        </button>
                        <button type="button" @click="toggleBookmark"
                            class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-200"
                            :class="isBookmarked ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-600'"
                            aria-label="সংরক্ষণ করুন">
                            <i class="bi text-sm" :class="isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark'"></i>
                        </button>

                        <a v-if="video.video_type === 'embed'" :href="video.video_url || '#'" target="_blank"
                            rel="noopener"
                            class="ml-1 flex items-center gap-1.5 bg-red-600 text-white text-sm font-medium px-3 py-1.5 rounded">
                            <i class="bi bi-youtube"></i>
                            Watch on YouTube
                        </a>

                        <span v-if="copied" class="text-xs text-green-600 ml-1">লিংক কপি হয়েছে</span>
                    </div>

                    <!-- Meta -->
                    <!-- CORRECTED: -->
                    <p class="mt-3 text-base text-gray-500">
                        প্রকাশ: {{ formattedDate }}
                    </p>

                    <!-- Description -->
                    <p v-if="video.description" class="mt-4 text-[20px] leading-7 text-gray-800 whitespace-pre-line">
                        {{ video.description }}
                    </p>
                </template>

                <div v-if="middleAds[0]"
                    class="w-full border-y border-gray-200 py-2 mt-10 mb-5 flex flex-col items-center cursor-pointer"
                    @click="trackClick(middleAds[0])">
                    <img :src="middleAds[0].image" :alt="middleAds[0].name" class="w-full max-w-[728px] h-auto object-contain" />
                    <span class="text-[10px] text-gray-400 mt-1">বিজ্ঞাপন — {{ middleAds[0].provider }}</span>
                </div>
            </div>

            <!-- ==================== SIDEBAR ==================== -->
            <aside class="min-w-0">

                <div class="space-y-5">
                    <div v-for="ad in sidebarAds" :key="ad.id" class="overflow-hidden cursor-pointer"
                        @click="trackClick(ad)">
                        <img :src="ad.image" :alt="ad.name" class="w-full h-auto" />
                        <div class="text-[10px] text-gray-400 mt-1 text-center">বিজ্ঞাপন — {{ ad.provider }}</div>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2 mb-3">
                    পরবর্তী ভিডিও
                </h2>

                <div v-if="relatedLoading" class="space-y-4">
                    <div v-for="n in 3" :key="n" class="flex gap-3 animate-pulse">
                        <div class="w-28 h-20 bg-gray-200 rounded shrink-0"></div>
                        <div class="flex-1 space-y-2 py-1">
                            <div class="h-3.5 bg-gray-200 rounded w-full"></div>
                            <div class="h-3.5 bg-gray-200 rounded w-2/3"></div>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col divide-y divide-gray-100">
                    <router-link v-for="item in relatedVideos" :key="item.id"
                        :to="{ name: 'video.show', params: { slug: item.slug || item.id } }"
                        class="flex gap-3 py-4 first:pt-0 group">
                        <div class="relative w-28 h-20 shrink-0 bg-gray-100 rounded overflow-hidden">
                            <img :src="item.thumbnail || ''" :alt="item.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" />
                            <div class="absolute inset-0 flex items-center justify-center bg-black/10">
                                <i class="bi bi-play-circle-fill text-white text-xl drop-shadow"></i>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-lg font-medium text-gray-800 leading-5 line-clamp-3 group-hover:text-red-600 transition-colors">
                                {{ item.title }}
                            </p>
                            <p class="mt-1.5 text-sm text-gray-400">{{ timeAgo(item.created_at) }}</p>
                        </div>
                    </router-link>

                    <p v-if="relatedVideos.length === 0" class="text-sm text-gray-400 py-4">
                        আর কোনো ভিডিও নেই।
                    </p>
                </div>
            </aside>
        </div>

        <div>
            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2 mb-3">
                আরও ভিডিও
            </h2>

            <div v-if="nextLoading" class="space-y-4">
                <div v-for="n in 3" :key="n" class="flex gap-3 animate-pulse">
                    <div class="w-28 h-20 bg-gray-200 rounded shrink-0"></div>
                    <div class="flex-1 space-y-2 py-1">
                        <div class="h-3.5 bg-gray-200 rounded w-full"></div>
                        <div class="h-3.5 bg-gray-200 rounded w-2/3"></div>
                    </div>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <router-link
                    v-for="item in nextVideos"
                    :key="item.id"
                    :to="{ name: 'video.show', params: { slug: item.slug || item.id } }"
                    class="group"
                >
                    <!-- Thumbnail -->
                    <div class="relative w-full aspect-video bg-gray-100 rounded overflow-hidden">
                        <img
                            :src="item.thumbnail || ''"
                            :alt="item.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                        />

                        <!-- Play Icon -->
                        <div class="absolute inset-0 flex items-center justify-center bg-black/10">
                            <i class="bi bi-play-circle-fill text-white text-3xl drop-shadow"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <p
                        class="mt-2 text-base font-medium text-gray-800 leading-5 line-clamp-2 group-hover:text-red-600 transition-colors"
                    >
                        {{ item.title }}
                    </p>

                    <!-- Time -->
                    <p class="mt-1 text-sm text-gray-400">
                        {{ timeAgo(item.created_at) }}
                    </p>
                </router-link>

                <p
                    v-if="nextVideos.length === 0"
                    class="col-span-full text-sm text-gray-400 py-4"
                >
                    আর কোনো ভিডিও নেই।
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from "vue";
import { useRoute } from "vue-router";
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

interface Advertisement {
    id: number
    image: string
    name: string
    provider: string
    link_url: string | null
    placement: 'top' | 'middle' | 'sidebar'
}

const topAds = ref<Advertisement[]>([])
const middleAds = ref<Advertisement[]>([])
const sidebarAds = ref<Advertisement[]>([])

const route = useRoute();

const video = ref<Video | null>(null);
const relatedVideos = ref<Video[]>([]);
const nextVideos = ref<Video[]>([]);
const loading = ref(true);
const relatedLoading = ref(true);
const nextLoading = ref(true);
const copied = ref(false);
const isBookmarked = ref(false);

const fetchAds = async () => {
    try {
        const [topRes, middleRes, sidebarRes] = await Promise.all([
            api.get('/advertisements', { params: { placement: 'top', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'middle', limit: 3 } }),
            api.get('/advertisements', { params: { placement: 'sidebar', limit: 2 } }),
        ])
        topAds.value = topRes.data.data ?? []
        middleAds.value = middleRes.data.data ?? []
        sidebarAds.value = sidebarRes.data.data ?? []
    } catch (error) {
        console.error('Failed to load advertisements:', error)
    }
}

// --- Extract slug from route, matching VideoList's :params="{ slug: video.slug || video.id }" ---
const slug = computed(() => route.params.slug as string);

// --- Unwrap Laravel Resource / paginated / direct payloads consistently ---
function unwrap(payload: any) {
    let data = payload;
    if (data?.data) data = data.data;
    if (data?.data) data = data.data;
    return data;
}

const fetchVideo = async () => {
    loading.value = true;
    video.value = null;
    try {
        const response = await api.get(`/video/${slug.value}`);
        video.value = unwrap(response?.data) || null;
    } catch (error) {
        console.error("Video fetch error:", error);
        video.value = null;
    } finally {
        loading.value = false;
    }
};

const fetchRelated = async () => {
    relatedLoading.value = true;
    try {
        const response = await api.get("/video", { params: { exclude: slug.value, limit: 5 } });
        let data = unwrap(response?.data);
        let list: Video[] = Array.isArray(data) ? data : [];
        // Fallback filter client-side in case the API doesn't support `exclude`
        list = list.filter((v) => (v.slug || String(v.id)) !== slug.value);
        relatedVideos.value = list.slice(0, 5);
    } catch (error) {
        console.error("Related videos fetch error:", error);
        relatedVideos.value = [];
    } finally {
        relatedLoading.value = false;
    }
};

const fetchNext = async () => {
    nextLoading.value = true;

    try {
        const response = await api.get("/video", {
            params: {
                exclude: slug.value,
                limit: 21,
            },
        });

        let data = unwrap(response?.data);

        let list: Video[] = Array.isArray(data) ? data : [];

        // Fallback filter client-side
        list = list.filter(
            (v) => (v.slug || String(v.id)) !== slug.value
        );

        nextVideos.value = list.slice(5, 21);

    } catch (error) {
        console.error("Related videos fetch error:", error);
        nextVideos.value = [];
    } finally {
        nextLoading.value = false;
    }
};

const loadAll = () => {
    fetchVideo();
    fetchRelated();
    fetchNext();
};

onMounted(loadAll);

// Handle navigating from one video page directly to another (component reuse)
watch(
    () => route.params.slug,
    () => loadAll()
);

// --- YouTube / Facebook embed URL builder ---
const embedUrl = computed(() => {
    const url = video.value?.video_url;
    if (!url) return null;

    const ytMatch = url.match(
        /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{11})/
    );
    if (ytMatch) return `https://www.youtube.com/embed/${ytMatch[1]}`;

    // Already an embeddable URL (e.g. already /embed/, Facebook plugin, Vimeo player, etc.)
    return url;
});

// --- Share links ---
const shareLinks = computed(() => {
    const pageUrl = typeof window !== "undefined" ? window.location.href : "";
    const title = video.value?.title || "";
    return {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(pageUrl)}`,
        twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(pageUrl)}&text=${encodeURIComponent(title)}`,
    };
});

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch (error) {
        console.error("Copy link failed:", error);
    }
};

const toggleBookmark = () => {
    isBookmarked.value = !isBookmarked.value;
    // TODO: wire up to a real bookmarks endpoint / localStorage as needed
};

// --- Bangla digit + date helpers ---

const toBanglaNumber = (num: number | string) => {
    const bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return num.toString().replace(/\d/g, (digit) => bengaliDigits[parseInt(digit)] ?? digit);
};

const parseUtcDate = (dateStr: string): Date => {
    const iso = dateStr.includes('T') ? dateStr : dateStr.replace(' ', 'T') + 'Z';
    return new Date(iso);
};

const banglaMonths = [
    "জানুয়ারি", "ফেব্রুয়ারি", "মার্চ", "এপ্রিল", "মে", "জুন",
    "জুলাই", "আগস্ট", "সেপ্টেম্বর", "অক্টোবর", "নভেম্বর", "ডিসেম্বর",
];


const formattedDate = computed(() => {
    if (!video.value?.created_at) return "";
    const d = parseUtcDate(video.value.created_at);
    if (isNaN(d.getTime())) return video.value.created_at;

    const day = toBanglaNumber(d.getDate());
    const month = banglaMonths[d.getMonth()];
    const year = toBanglaNumber(d.getFullYear());
    let hours = d.getHours();
    const minutes = d.getMinutes();
    const period = hours >= 12 ? "অপরাহ্ণ" : "পূর্বাহ্ণ";
    hours = hours % 12 || 12;
    const hh = toBanglaNumber(String(hours).padStart(2, "0"));
    const mm = toBanglaNumber(String(minutes).padStart(2, "0"));
    return `${day} ${month} ${year}, ${hh}:${mm} ${period}`;
});

const timeAgo = (dateStr?: string | null) => {
    if (!dateStr) return '';
    const diffMs = Date.now() - parseUtcDate(dateStr).getTime();
    const mins = Math.floor(diffMs / 60000);
    if (mins < 1) return 'এইমাত্র';
    if (mins < 60) return `${toBanglaNumber(mins)} মিনিট আগে`;
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${toBanglaNumber(hours)} ঘণ্টা আগে`;
    const days = Math.floor(hours / 24);
    return `${toBanglaNumber(days)} দিন আগে`;
};

const trackClick = async (ad: Advertisement) => {
    if (!ad.link_url) return
    try {
        await api.post(`/advertisements/${ad.id}/click`)
    } catch { /* non-blocking */ }
    window.open(ad.link_url, '_blank')
}

onMounted(() => {
    fetchAds()

})
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>