<template>
    <div class="max-w-7xl mx-auto px-4 bg-white border-b border-gray-200 pt-4 pb-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- ফিচার: slider column -->
            <div class="lg:col-span-3 flex flex-col gap-3 border-r border-gray-300 p-2">
                <h2 class="font-bold text-xl border-b-2 border-red-600 inline-block pb-1 w-fit">
                    ফিচার
                </h2>

                <div class="relative w-full h-72 aspect-[4/3] overflow-hidden rounded-md bg-gray-100">
                <router-link
                    v-for="(slide, i) in featureSlides"
                    :key="slide.slug"
                    :to="`/article/${slide.slug}`"
                    class="absolute inset-0 block transition-opacity duration-700 ease-in-out"
                    :class="i === activeSlide ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
                >
                    <img
                        :src="slide.src"
                        :alt="slide.alt"
                        class="absolute inset-0 w-full h-full object-cover"
                    />
                    <!-- title overlay -->
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent px-3 pt-8 pb-3">
                        <h3 class="text-white text-lg font-semibold leading-snug line-clamp-2">
                            {{ slide.title }}
                        </h3>
                    </div>
                </router-link>

    <!-- dots -->
    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
        <button v-for="(slide, i) in featureSlides" :key="'dot-' + i" @click="activeSlide = i"
            class="w-2 h-2 rounded-full transition-colors"
            :class="i === activeSlide ? 'bg-white' : 'bg-white/50'" />
    </div>
</div>
            </div>

            <!-- মতামত -->
            <div class="lg:col-span-4 border-r border-gray-300 p-2">
                <h2 class="font-bold text-xl border-b-2 border-red-600 inline-block pb-1 w-fit mb-3">
                    মতামত
                </h2>

                <div>
                    <img src="/images/topimage.png" alt="মতামত"
                        class="float-left w-16 h-16 object-cover rounded-md mr-3 mb-1" />
                    <p class="text-base leading-relaxed text-gray-800">
                        প্রধানমন্ত্রী তারেক রহমান বলেছেন, ‘আমরা ৭১ সালে যুদ্ধ করে দেশ স্বাধীন করেছি।
                        পরবর্তী সময়ে যখনই গণতন্ত্র হুমকির মুখে পড়েছে, জনগণ রাজপথে নেমে এসেছে,
                        আন্দোলন করেছে, বারবার গণতন্ত্রকে ফিরিয়ে নিয়ে এসেছে। এখন হচ্ছে আমাদের
                        দেশ গঠন করার সময়। বাংলাদেশের ২০ কোটি মানুষকে সচেতন থাকতে হবে। এখন হচ্ছে আমাদের
                        দেশ গঠন করার সময়। বাংলাদেশের ২০ কোটি মানুষকে সচেতন থাকতে হবে।
                    </p>
                    <!-- clears the float so the next block in the page doesn't ride up beside the image -->
                    <div class="clear-both"></div>
                </div>
            </div>

            <!-- সর্বাধিক পঠিত -->
            <div class="lg:col-span-5 flex flex-col gap-3 p-2">
                <h2 class="font-bold text-xl border-b-2 border-red-600 inline-block pb-1 w-fit">
                    সর্বাধিক পঠিত
                </h2>
                <ul class="divide-y divide-gray-100">
                        <li
                            v-for="(item, i) in sidePanelList"
                            :key="item.id"
                            class="py-3 flex items-start gap-3 group"
                        >
                            <span class="text-2xl font-bold text-gray-400 leading-none shrink-0 w-5">
                                {{ toBengaliNumber(i + 1) }}
                            </span>
                            <router-link
                                :to="`/article/${item.slug}`"
                                class="text-lg font-semibold text-gray-800 group-hover:text-red-600 leading-snug"
                            >
                                {{ item.title }}
                            </router-link>
                        </li>
                    </ul>
            </div>

        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()

interface Article {
    id: number;
    title: string;
    slug: string;
    excerpt?: string | null;
    featured_image: string | null;
    published_at: string | null;
    is_video?: boolean;
    is_featured?: boolean;
    category?: { id: number; name: string; slug: string };
    sub_category?: { id: number; name: string; slug: string } | null;
}

interface Slide {
    src: string;
    alt: string;
    slug: string;
    title: string;
}
const featureSlides = ref<Slide[]>([])
const activeSlide = ref(0)
let slideTimer: ReturnType<typeof setInterval> | null = null

const startSlideshow = () => {
    stopSlideshow()
    if (featureSlides.value.length <= 1) return
    slideTimer = setInterval(() => {
        activeSlide.value = (activeSlide.value + 1) % featureSlides.value.length
    }, 4000)
}

const stopSlideshow = () => {
    if (slideTimer) {
        clearInterval(slideTimer)
        slideTimer = null
    }
}



// --- article lists ---
const articles = ref<Article[]>([])
const featuredList = ref<Article[]>([])
const popularList = ref<Article[]>([])
const category = ref<{ id: number; name: string; slug: string } | null>(null)
const loading = ref(false)

const meta = reactive({
    current_page: 1,
    last_page: 1,
})

// --- side panel tab toggle ('featured' | 'popular') ---
const sideTab = ref<'featured' | 'popular'>('popular')

const sidePanelList = computed(() =>
    (sideTab.value === 'featured' ? featuredList.value : popularList.value).slice(0, 5)
)

const bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']
const toBengaliNumber = (n: number): string =>
    String(n).split('').map(d => bengaliDigits[Number(d)] ?? d).join('')

const fetchArticles = async (page = 1) => {
    loading.value = true
    try {
        const slug = route.params.slug as string | undefined
        const endpoint = slug ? `/categories/${slug}/articles` : '/articles'
        const popularEndpoint = slug ? `/categories/${slug}/popular` : '/articles/popular'

        const { data } = await api.get(endpoint, { params: { page, per_page: 15 } })

        articles.value = data.data ?? []
        category.value = data.category ?? null
        meta.current_page = data.meta?.current_page ?? 1
        meta.last_page = data.meta?.last_page ?? 1

        featuredList.value = articles.value.filter((a) => a.is_featured)

        // last 5 featured articles → slider slides
        featureSlides.value = featuredList.value
        .slice(0, 5)
        .filter((a) => a.featured_image)
        .map((a) => ({
            src: a.featured_image as string,
            alt: a.title,
            slug: a.slug,
            title: a.title,
        }))
        startSlideshow()

        try {
            const { data: popularData } = await api.get(popularEndpoint)
            popularList.value = popularData.data ?? []
        } catch (popularError) {
            console.error('Failed to load popular articles:', popularError)
            popularList.value = []
        }
    } catch (error) {
        console.error('Failed to load articles:', error)
        articles.value = []
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchArticles()
})

onUnmounted(() => {
    stopSlideshow()
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>