//article/[slug].vue
<template>
    <div class="max-w-7xl mx-auto px-4 py-6 text-gray-900">

        <div v-if="topAds[0]"
            class="w-full border-y border-gray-200 py-2 mt-10 sm:mt-0 mb-5 flex flex-col items-center cursor-pointer"
            @click="trackClick(topAds[0])">
            <img :src="topAds[0].image" :alt="topAds[0].name" class="w-full max-w-[728px] h-auto object-contain" />
            <span class="text-[10px] text-gray-400 mt-1">বিজ্ঞাপন — {{ topAds[0].provider }}</span>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-20 text-gray-400">
            <i class="bi bi-arrow-repeat animate-spin text-3xl"></i>
        </div>

        <!-- Article -->
        <template v-else-if="article">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- LEFT / MAIN ARTICLE -->
                <main class="lg:col-span-9">

                    <!-- Category -->
                    <div class="flex items-center gap-2 text-base sm:text-lg mb-3">
                        <i class="bi bi-file-earmark-ruled text-red-600"></i>
                        <router-link v-if="article.category" :to="`/category/${article.category.slug}`"
                            class="text-red-600 font-semibold hover:underline">
                            {{ article.category.name }}
                        </router-link>
                        <span v-if="article.sub_category" class="text-gray-400">›</span>
                        <router-link v-if="article.sub_category" :to="`/category/${article.sub_category.slug}`"
                            class="text-gray-500 hover:underline">
                            {{ article.sub_category.name }}
                        </router-link>
                    </div>

                    <h1 class="text-base sm:text-lg  text-gray-900 pt-4 leading-tight mb-2">
                        {{ article.sub_title }}
                    </h1>

                    <!-- TITLE -->
                    <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-bold text-gray-900leading-tight mb-4">
                        {{ article.title }}
                    </h1>

                    <!-- AUTHOR + DATE -->
                    <div class="mb-4">
                        <div class="text-base font-medium text-gray-800 mb-1">খবরজোন অনলাইন</div>
                        <div class="text-sm text-gray-500">
                            <span class="font-medium">প্রকাশ :</span>
                            {{ formatBanglaDate(article.published_at) }}
                        </div>
                    </div>

                    <!-- SHARE BAR -->
                    <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-3">
                        <div class="flex items-center gap-2">
                            <button class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                                <i class="bi bi-facebook"></i>
                            </button>
                            <button class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                                <i class="bi bi-twitter-x"></i>
                            </button>
                            <button class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                                <i class="bi bi-whatsapp"></i>
                            </button>
                            <button class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                                <i class="bi bi-telegram"></i>
                            </button>
                            <button class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                                <i class="bi bi-linkedin"></i>
                            </button>
                            <button class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                                <i class="bi bi-envelope-fill"></i>
                            </button>
                        </div>
                    </div>

                    <!-- FEATURED IMAGE -->
                    <div v-if="article.featured_image" class="w-full overflow-hidden mb-1">
                        <img :src="article.featured_image" :alt="article.title" class="w-full h-auto object-cover" />
                    </div>

                    <!-- Image Caption -->
                    <div class="flex flex-row text-lg text-gray-700">
                        <div v-if="article.image_title" class="text-base text-gray-500 pb-2 mb-5">
                        {{ article.image_title }} <span class="text-red-700">|</span>
                    </div>
                    <div v-if="article.image_author" class="text-base text-gray-500 pb-2 pl-2 mb-5">
                         ছবি: {{ article.image_author }} 
                    </div>
                    </div>

                    <!-- MIDDLE BANNER (dynamic) -->
                    <div
                        v-if="middleAds[0]"
                        class="w-full border-y border-gray-200 py-2 mb-5 flex flex-col items-center cursor-pointer"
                        @click="trackClick(middleAds[0])"
                    >
                        <img :src="middleAds[0].image" :alt="middleAds[0].name" class="w-full max-w-[728px] h-auto object-contain" />
                        <span class="text-[10px] text-gray-400 mt-1">বিজ্ঞাপন — {{ middleAds[0].provider }}</span>
                    </div>

                    <!-- CONTENT -->
                    <div class="max-w-2xl mx-auto">
                        <div
                            v-if="article.excerpt"
                            class="whitespace-pre-line text-lg sm:text-xl text-gray-900 font-medium leading-relaxed space-y-4"
                        >
                            {{ article.excerpt }}
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div class="max-w-2xl mx-auto mt-4">
                        <div
                            v-if="article.content"
                            class="whitespace-pre-line text-lg sm:text-xl text-gray-900 font-medium leading-relaxed space-y-4"
                        >
                            {{ article.content }}
                        </div>
                    </div>

                    <!-- GALLERY IMAGE 1 -->
                    <div v-if="galleryImage(0)" class="max-w-2xl mx-auto mt-4">
                        <img
                            :src="galleryImage(0)!.url"
                            :alt="galleryImage(0)!.caption ?? article.title"
                            class="w-full h-auto object-cover rounded"
                        />
                        <p v-if="galleryImage(0)!.caption" class="text-sm text-gray-500 mt-1">
                            {{ galleryImage(0)!.caption }}
                        </p>
                    </div>

                     <!-- MIDDLE TWO BANNER (dynamic) -->
                    <div
                        v-if="middleTwoAds[0]"
                        class="w-full flex flex-col items-center m-2 cursor-pointer"
                        @click="trackClick(middleTwoAds[0])"
                    >
                        <img :src="middleTwoAds[0].image" :alt="middleTwoAds[0].name" class="w-full max-w-[728px] h-64 object-contain" />
                        <span class="text-[10px] text-gray-400">বিজ্ঞাপন — {{ middleTwoAds[0].provider }}</span>
                    </div>

                    <!-- CONTENT Two -->
                    <div class="max-w-2xl mx-auto mt-4">
                        <div
                            v-if="article.content_two"
                            class="whitespace-pre-line text-lg sm:text-xl text-gray-900 font-medium leading-relaxed space-y-4"
                        >
                            {{ article.content_two }}
                        </div>
                    </div>

                    <!-- GALLERY IMAGE 2 -->
                    <div v-if="galleryImage(1)" class="max-w-2xl mx-auto mt-4">
                        <img
                            :src="galleryImage(1)!.url"
                            :alt="galleryImage(1)!.caption ?? article.title"
                            class="w-full h-auto object-cover rounded"
                        />
                        <p v-if="galleryImage(1)!.caption" class="text-sm text-gray-500 mt-1">
                            {{ galleryImage(1)!.caption }}
                        </p>
                    </div>

                     <!-- MIDDLE Third BANNER (dynamic) -->
                    <div
                        v-if="middleThreeAds[0]"
                        class="w-full flex flex-col items-center p-4 cursor-pointer"
                        @click="trackClick(middleThreeAds[0])"
                    >
                        <img :src="middleThreeAds[0].image" :alt="middleThreeAds[0].name" class="w-full object-contain" />
                        <span class="text-[10px] text-gray-400">বিজ্ঞাপন — {{ middleThreeAds[0].provider }}</span>
                    </div>

                    <!-- CONTENT THREE -->
                    <div class="max-w-2xl mx-auto mt-4">
                        <div
                            v-if="article.content_three"
                            class="whitespace-pre-line text-lg sm:text-xl text-gray-900 font-medium leading-relaxed space-y-4"
                        >
                            {{ article.content_three }}
                        </div>
                    </div>

                    <!-- GALLERY IMAGE 3 -->
                    <div v-if="galleryImage(2)" class="max-w-2xl mx-auto mt-4">
                        <img
                            :src="galleryImage(2)!.url"
                            :alt="galleryImage(2)!.caption ?? article.title"
                            class="w-full h-auto object-cover rounded"
                        />
                        <p v-if="galleryImage(2)!.caption" class="text-sm text-gray-500 mt-1">
                            {{ galleryImage(2)!.caption }}
                        </p>
                    </div>

                    <!-- RELATED ARTICLES -->
                    <div v-if="related.length" class="mt-12 pt-6 border-t border-gray-200">
                        <h2 class="text-xl font-bold text-red-600 mb-5">সম্পর্কিত সংবাদ</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <router-link v-for="item in related" :key="item.id" :to="`/article/${item.slug}`" class="group">
                                <div class="relative h-44 overflow-hidden rounded">
                                    <img v-if="item.featured_image" :src="item.featured_image" :alt="item.title"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                                    <div v-else class="w-full h-full bg-gray-200"></div>
                                </div>
                                <h3 class="mt-2 font-bold text-sm leading-snug group-hover:text-red-600">
                                    {{ item.title }}
                                </h3>
                            </router-link>
                        </div>
                    </div>

                </main>

                <!-- RIGHT SIDEBAR -->
                <aside class="lg:col-span-3 border-l pl-3">
                    <div class="lg:sticky lg:top-20">

                        <div class="space-y-5">
                            <div
                                v-for="ad in sidebarAds"
                                :key="ad.id"
                                class="overflow-hidden cursor-pointer"
                                @click="trackClick(ad)"
                            >
                                <img :src="ad.image" :alt="ad.name" class="w-full h-auto" />
                                <div class="text-[10px] text-gray-400 mt-1 text-center">বিজ্ঞাপন — {{ ad.provider }}</div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h2 class="text-2xl font-bold text-gray-600 border-b border-gray-300 pb-2 mt-3 mb-3">
                                সর্বশেষ খবর
                            </h2>

                            <router-link
                                v-for="item in latestArticles"
                                :key="item.id"
                                :to="`/article/${item.slug}`"
                                class="flex flex-col gap-3 py-3 border-b border-gray-200 group"
                            >
                                <div class="w-full h-36 flex-shrink-0 overflow-hidden rounded">
                                    <img
                                        v-if="item.featured_image"
                                        :src="item.featured_image"
                                        :alt="item.title"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full bg-gray-200"></div>
                                </div>
                                <div class="flex flex-col justify-between">
                                    <h3 class="text-xl text-gray-800 group-hover:text-red-600">
                                        {{ item.title }}
                                    </h3>
                                    <span class="text-[14px] text-gray-400 mt-1">
                                        {{ timeAgo(item.published_at) }}
                                    </span>
                                </div>
                            </router-link>
                        </div>

                        <div
                            v-for="ad in sidebarTwoAds"
                            :key="ad.id"
                            class="overflow-hidden cursor-pointer"
                            @click="trackClick(ad)"
                        >
                            <img :src="ad.image" :alt="ad.name" class="w-full h-auto" />
                            <div class="text-[10px] text-gray-400 mt-1 text-center">বিজ্ঞাপন — {{ ad.provider }}</div>
                        </div>

                        <div>
                            <router-link
                                v-for="item in latestBottomArticles"
                                :key="item.id"
                                :to="`/article/${item.slug}`"
                                class="flex flex-col gap-3 py-3 border-b border-gray-200 group"
                            >
                                <div class="w-full h-36 flex-shrink-0 overflow-hidden rounded">
                                    <img
                                        v-if="item.featured_image"
                                        :src="item.featured_image"
                                        :alt="item.title"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full bg-gray-200"></div>
                                </div>
                                <div class="flex flex-col justify-between">
                                    <h3 class="text-xl text-gray-800 group-hover:text-red-600">
                                        {{ item.title }}
                                    </h3>
                                    <span class="text-[14px] text-gray-400 mt-1">
                                        {{ timeAgo(item.published_at) }}
                                    </span>
                                </div>
                            </router-link>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 p-3">
                            <h3 class="text-sm font-semibold text-red-600 mb-2">বিশেষ সংবাদ</h3>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                সর্বশেষ সংবাদ ও গুরুত্বপূর্ণ আপডেট জানতে খবরজোনের সাথে থাকুন।
                            </p>
                        </div>

                    </div>
                </aside>

            </div>

            <!-- SAME CATEGORY — MORE NEWS (now inside the article branch, will actually render) -->
            <div v-if="sameCategoryArticles.length" class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-bold text-red-600 mb-4">আরও সংবাদ</h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <router-link
                        v-for="item in sameCategoryArticles"
                        :key="item.id"
                        :to="`/article/${item.slug}`"
                        class="group"
                    >
                        <div class="relative h-28 overflow-hidden rounded">
                            <img
                                v-if="item.featured_image"
                                :src="item.featured_image"
                                :alt="item.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                            />
                            <div v-else class="w-full h-full bg-gray-200"></div>
                        </div>
                        <h4 class="mt-2 text-xs font-semibold leading-snug text-gray-800 group-hover:text-red-600 line-clamp-2">
                            {{ item.title }}
                        </h4>
                    </router-link>
                </div>
            </div>

        </template>

        <!-- No article -->
        <div v-else class="text-center py-20 text-gray-400">
            আর্টিকেল পাওয়া যায়নি
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

interface ArticleImage {
    id: number
    url: string
    caption: string | null
    sort_order: number
    is_cover: boolean
}

interface Article {
    id: number
    title: string
    slug: string
    sub_title: string
    content?: string
    content_two?: string
    content_three?: string
    excerpt?: string | null
    featured_image: string | null
    image_title: string | null
    image_author: string | null
    published_at: string | null
    category?: { id: number; name: string; slug: string }
    sub_category?: { id: number; name: string; slug: string } | null
    author?: { id: number; name: string }
    images?: ArticleImage[]  
}

const route = useRoute()
const article = ref<Article | null>(null)
const related = ref<Article[]>([])
const latestArticles = ref<Article[]>([])
const latestBottomArticles = ref<Article[]>([])
const loading = ref(false)
const sameCategoryArticles = ref<Article[]>([])

const galleryImage = (index: number): ArticleImage | null => {
    return article.value?.images?.[index] ?? null
}


interface Advertisement {
    id: number
    image: string
    name: string
    provider: string
    link_url: string | null
    placement: 'top' | 'middle' | 'sidebar' | 'middle-two' | 'middle-three' | 'sidebar-two'
}

const topAds = ref<Advertisement[]>([])
const middleAds = ref<Advertisement[]>([])
const middleTwoAds = ref<Advertisement[]>([])
const middleThreeAds = ref<Advertisement[]>([])
const sidebarAds = ref<Advertisement[]>([])
const sidebarTwoAds = ref<Advertisement[]>([])


const fetchLatestArticles = async () => {
    try {
        const { data } = await api.get('/articles', {
            params: { per_page: 3 }, 
        })

        latestArticles.value = (data.data ?? []).filter(
            (item: Article) => item.slug !== route.params.slug
        )
    } catch (error) {
        console.error('Failed to load latest articles:', error)
    }
}

const fetchLatestBottomArticles = async () => {
    try {
        const { data } = await api.get('/articles', {
            params: {
                limit: 6,
            },
        })

        latestBottomArticles.value = (data.data ?? [])
            .filter(
                (item: Article) => item.slug !== route.params.slug
            )
            .slice(3, 6)

    } catch (error) {
        console.error('Failed to load latest articles:', error)
    }
}

const timeAgo = (date: string | null): string => {
    if (!date) return ''

    const now = new Date()
    const then = new Date(date)
    const seconds = Math.floor((now.getTime() - then.getTime()) / 1000)

    if (seconds < 60) return 'এইমাত্র'

    const minutes = Math.floor(seconds / 60)
    if (minutes < 60) return `${minutes} মিনিট আগে`

    const hours = Math.floor(minutes / 60)
    if (hours < 24) return `${hours} ঘণ্টা আগে`

    const days = Math.floor(hours / 24)
    if (days < 30) return `${days} দিন আগে`

    const months = Math.floor(days / 30)
    if (months < 12) return `${months} মাস আগে`

    const years = Math.floor(months / 12)
    return `${years} বছর আগে`
}

const formatBanglaDate = (date: string | null) => {
    if (!date) return ''
    const d = new Date(date)
    const datePart = new Intl.DateTimeFormat('bn-BD', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
    }).format(d).replace(/,/g, '').replace(/ এ$/, '')
    const timePart = new Intl.DateTimeFormat('bn-BD', {
        hour: '2-digit', minute: '2-digit', hour12: false,
    }).format(d)
    return `${datePart} ; ${timePart}`
}

const fetchAds = async () => {
    try {
        const [topRes, middleRes, middleTwoRes, middleThreeRes, sidebarRes, sidebarTwoRes] = await Promise.all([
            api.get('/advertisements', { params: { placement: 'top', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'middle', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'middle-two', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'middle-three', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'sidebar', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'sidebar-two', limit: 1 } }),
        ])
        topAds.value = topRes.data.data ?? []
        middleAds.value = middleRes.data.data ?? []
        middleTwoAds.value = middleTwoRes.data.data ?? []
        middleThreeAds.value = middleThreeRes.data.data ?? []
        sidebarAds.value = sidebarRes.data.data ?? []
        sidebarTwoAds.value = sidebarTwoRes.data.data ?? []

        
    } catch (error) {
        console.error('Failed to load advertisements:', error)
    }
}



const fetchArticle = async () => {
    loading.value = true
    article.value = null
    try {
        const slug = route.params.slug as string
        const { data } = await api.get(`/articles/${slug}`)
        article.value = data.data
        related.value = data.related?.data ?? []
        sameCategoryArticles.value = (data.related?.data ?? []).slice(0, 8)
    } catch (error) {
        console.error('Failed to load article:', error)
    } finally {
        loading.value = false
        window.scrollTo({ top: 0, behavior: 'auto' })
    }
}



const trackClick = async (ad: Advertisement) => {
    if (!ad.link_url) return
    try {
        await api.post(`/advertisements/${ad.id}/click`)
    } catch { /* non-blocking */ }
    window.open(ad.link_url, '_blank')
}


watch(() => route.params.slug, () => {
    fetchArticle()
    fetchLatestArticles()
    fetchLatestBottomArticles()
})

onMounted(() => {
    fetchArticle()
    fetchAds()
    fetchLatestArticles()
    fetchLatestBottomArticles()
})
</script>

<style scoped>

</style>