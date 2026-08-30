//opinion/[slug].vue
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

        <!-- Opinion -->
        <template v-else-if="opinion">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- LEFT / MAIN CONTENT -->
                <main class="lg:col-span-9">

                    <!-- Section label -->
                    <div class="flex items-center gap-2 text-base sm:text-lg mb-3">
                        <i class="bi bi-file-earmark-ruled text-red-600"></i>
                        <span class="text-red-600 font-semibold">মতামত</span>
                    </div>

                    <!-- TITLE -->
                    <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-bold text-gray-900 leading-tight mb-4">
                        {{ opinion.title }}
                    </h1>

                    <!-- WRITER + DATE -->
                    <div class="flex items-center gap-3 mb-4">
                        <img
                            v-if="opinion.writer_image"
                            :src="opinion.writer_image"
                            :alt="opinion.writer_name"
                            class="w-12 h-12 rounded-full object-cover"
                        />
                        <div>
                            <div class="text-base font-medium text-gray-800">
                                {{ opinion.writer_name }}
                                <span v-if="opinion.writer_designation" class="text-gray-400 font-normal">
                                    · {{ opinion.writer_designation }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">প্রকাশ :</span>
                                {{ formatBanglaDate(opinion.published_at) }}
                            </div>
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
                    <div v-if="opinion.image" class="w-full overflow-hidden mb-5">
                        <img :src="opinion.image" :alt="opinion.title" class="w-full h-auto object-cover" />
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

                    <!-- TEXT -->
                    <div class="max-w-2xl mx-auto">
                        <div
                            class="whitespace-pre-line text-lg sm:text-xl text-gray-900 font-medium leading-relaxed space-y-4"
                        >
                            {{ opinion.text }}
                        </div>
                    </div>

                    <!-- RELATED OPINIONS -->
                    <div v-if="relatedOpinions.length" class="mt-12 pt-6 border-t border-gray-200">
                        <h2 class="text-xl font-bold text-red-600 mb-5">আরও মতামত</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <router-link v-for="item in relatedOpinions" :key="item.id" :to="`/opinion/${item.slug}`" class="group">
                                <div class="relative h-44 overflow-hidden rounded">
                                    <img v-if="item.image" :src="item.image" :alt="item.title"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                                    <div v-else class="w-full h-full bg-gray-200"></div>
                                </div>
                                <h3 class="mt-2 font-bold text-sm leading-snug group-hover:text-red-600">
                                    {{ item.title }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">{{ item.writer_name }}</p>
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
                                সাম্প্রতিক মতামত
                            </h2>

                            <router-link
                                v-for="item in latestOpinions"
                                :key="item.id"
                                :to="`/opinion/${item.slug}`"
                                class="flex flex-col gap-3 py-3 border-b border-gray-200 group"
                            >
                                <div class="w-full h-36 flex-shrink-0 overflow-hidden rounded">
                                    <img
                                        v-if="item.image"
                                        :src="item.image"
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
                                        {{ item.writer_name }} · {{ timeAgo(item.published_at) }}
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

                        <div class="bg-gray-50 border border-gray-200 p-3">
                            <h3 class="text-sm font-semibold text-red-600 mb-2">বিশেষ সংবাদ</h3>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                সর্বশেষ সংবাদ ও গুরুত্বপূর্ণ আপডেট জানতে খবরজোনের সাথে থাকুন।
                            </p>
                        </div>

                    </div>
                </aside>

            </div>

        </template>

        <!-- No opinion -->
        <div v-else class="text-center py-20 text-gray-400">
            মতামত পাওয়া যায়নি
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

interface Opinion {
    id: number
    title: string
    slug: string
    writer_name: string
    writer_designation: string | null
    writer_image: string | null
    text: string
    image: string | null
    is_published: boolean
    published_at: string | null
    sort_order: number
}

const route = useRoute()
const opinion = ref<Opinion | null>(null)
const latestOpinions = ref<Opinion[]>([])
const relatedOpinions = ref<Opinion[]>([])
const loading = ref(false)

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
const sidebarAds = ref<Advertisement[]>([])
const sidebarTwoAds = ref<Advertisement[]>([])

const fetchOpinion = async () => {
    loading.value = true
    opinion.value = null
    try {
        const slug = route.params.slug as string
        const { data } = await api.get(`/opinions/${slug}`)
        opinion.value = data.data
    } catch (error) {
        console.error('Failed to load opinion:', error)
    } finally {
        loading.value = false
        window.scrollTo({ top: 0, behavior: 'auto' })
    }
}

const fetchLatestOpinions = async () => {
    try {
        const { data } = await api.get('/opinions/latest', { params: { limit: 6 } })
        const list = (data.data ?? []).filter(
            (item: Opinion) => item.slug !== route.params.slug
        )
        latestOpinions.value = list.slice(0, 4)
        relatedOpinions.value = list.slice(0, 3)
    } catch (error) {
        console.error('Failed to load latest opinions:', error)
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
        const [topRes, middleRes, sidebarRes, sidebarTwoRes] = await Promise.all([
            api.get('/advertisements', { params: { placement: 'top', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'middle', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'sidebar', limit: 1 } }),
            api.get('/advertisements', { params: { placement: 'sidebar-two', limit: 1 } }),
        ])
        topAds.value = topRes.data.data ?? []
        middleAds.value = middleRes.data.data ?? []
        sidebarAds.value = sidebarRes.data.data ?? []
        sidebarTwoAds.value = sidebarTwoRes.data.data ?? []
    } catch (error) {
        console.error('Failed to load advertisements:', error)
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
    fetchOpinion()
    fetchLatestOpinions()
})

onMounted(() => {
    fetchOpinion()
    fetchAds()
    fetchLatestOpinions()
})
</script>

<style scoped>
</style>