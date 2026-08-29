<template>
    <div class="max-w-7xl mx-auto px-4 bg-white text-gray-900 border-b border-gray-200 pt-4 pb-4">

        <!-- Main 12-Column Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left Section: News Cards + YouTube Banner (8 Cols) -->
            <div class="lg:col-span-9 space-y-6 lg:border-r lg:border-gray-200 lg:pr-6">

                <!-- 4-Column News Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">

                    <!-- Article 1 -->
                    <article v-if="bangladeshArticle"
                        class="space-y-2 border-b sm:border-b-0 sm:border-r border-gray-200 pb-3 sm:pb-0 sm:pr-3">
                        <router-link :to="`/article/${bangladeshArticle.slug}`">
                            <div class="aspect-[16/10] w-full overflow-hidden rounded bg-gray-100">
                                <img :src="bangladeshArticle.featured_image || ''" alt=""
                                    class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
                            </div>
                            <h3
                                class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer mt-2">
                                <span>{{ bangladeshArticle.title }}</span>
                            </h3>
                        </router-link>
                    </article>

                    <!-- Article 2 -->
                    <article v-if="biswArticle"
                        class="space-y-2 border-b sm:border-b-0 sm:border-r border-gray-200 pb-3 sm:pb-0 sm:pr-3">
                        <router-link :to="`/article/${biswArticle.slug}`">
                            <div class="aspect-[16/10] w-full overflow-hidden rounded bg-gray-100">
                                <img :src="biswArticle.featured_image || ''" alt=""
                                    class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
                            </div>
                            <h3
                                class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer mt-2">
                                <span>{{ biswArticle.title }}</span>
                            </h3>
                        </router-link>
                    </article>

                    <!-- Article 3 -->
                    <article v-if="arthoneetiArticle"
                        class="space-y-2 border-b sm:border-b-0 sm:border-r border-gray-200 pb-3 sm:pb-0 sm:pr-3">
                        <router-link :to="`/article/${arthoneetiArticle.slug}`">
                            <div class="aspect-[16/10] w-full overflow-hidden rounded bg-gray-100">
                                <img :src="arthoneetiArticle.featured_image || ''" alt=""
                                    class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
                            </div>
                            <h3
                                class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer mt-2">
                                <span>{{ arthoneetiArticle.title }}</span>
                            </h3>
                        </router-link>
                    </article>

                    <!-- Article 4 -->
                    <article v-if="sportsArticle" class="space-y-2 border-b sm:border-b-0 border-gray-200 pb-3 sm:pb-0">
                        <router-link :to="`/article/${sportsArticle.slug}`">
                            <div class="aspect-[16/10] w-full overflow-hidden rounded bg-gray-100">
                                <img :src="sportsArticle.featured_image || ''" alt=""
                                    class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
                            </div>
                            <h3
                                class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer mt-2">
                                <span>{{ sportsArticle.title }}</span>
                            </h3>
                        </router-link>
                    </article>
                    

                </div>

                <div class="p-3 flex items-center justify-between cursor-pointer shadow">
                    <div class="flex items-center space-x-3">
                        <div v-if="middleTwoAds[0]"
                            class="w-full border-y border-gray-200 py-2 mt-20 sm:mt-0 mb-5 flex flex-col items-center cursor-pointer"
                            @click="trackClick(middleTwoAds[0])">
                            <img :src="middleTwoAds[0].image" :alt="middleTwoAds[0].name" class="w-full max-w-[728px] h-auto object-contain" />
                            <span class="text-[10px] text-gray-400 mt-1">বিজ্ঞাপন — </span>
                        </div>
                    </div>
                </div>
                

            </div>

            <!-- Right Section: Sidebar Ads (4 Cols) -->
            <div class="lg:col-span-3 space-y-4">

                <!-- Side Banner four -->
                <div v-if="sideFourAds[0]"
                    class="w-full border-y border-gray-200 py-2 mt-20 sm:mt-0 mb-5 flex flex-col items-center cursor-pointer"
                    @click="trackClick(sideFourAds[0])">
                    <img :src="sideFourAds[0].image" :alt="sideFourAds[0].name"
                        class="w-full max-w-[728px] h-auto object-contain" />
                    <span class="text-[10px] text-gray-400 mt-1">বিজ্ঞাপন — </span>
                </div>

                <!-- Side Banner five -->
                <div v-if="sideFiveAds[0]"
                    class="w-full border-y border-gray-200 py-2 mt-20 sm:mt-0 mb-5 flex flex-col items-center cursor-pointer"
                    @click="trackClick(sideFiveAds[0])">
                    <img :src="sideFiveAds[0].image" :alt="sideFiveAds[0].name"
                        class="w-full max-w-[728px] h-auto object-contain" />
                    <span class="text-[10px] text-gray-400 mt-1">বিজ্ঞাপন — </span>
                </div>

            </div>

        </div>

    </div>

</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/services/api'

interface Article {
    id: number
    title: string
    slug: string
    sub_title: string | null
    excerpt: string | null
    content: string | null
    featured_image: string | null
    status: string
    is_featured: boolean
    is_breaking: boolean
    views: number
    published_at: string | null
    created_at: string
    updated_at: string
    category?: any
    sub_category?: any
    author?: { id: number; name: string }
    editor?: { id: number; name: string }
}

const bangladeshArticle = ref<Article | null>(null)
const biswArticle = ref<Article | null>(null)
const arthoneetiArticle = ref<Article | null>(null)
const sportsArticle = ref<Article | null>(null)

const fetchArticles = async () => {
    try {
        const [bangladeshRes, biswRes, arthoneetiRes, sportsRes] = await Promise.all([
            api.get('/articles/category/bangladesh/latest'),
            api.get('/articles/category/bisw/latest'),
            api.get('/articles/category/arthoneeti/latest'),
            api.get('/articles/category/sports/latest')
        ])

        bangladeshArticle.value = bangladeshRes.data.data ?? null
        biswArticle.value = biswRes.data.data ?? null
        arthoneetiArticle.value = arthoneetiRes.data.data ?? null
        sportsArticle.value = sportsRes.data.data ?? null

    } catch (error) {
        console.error('Failed to load articles:', error)
    }
}

interface Advertisement {
    id: number
    image: string
    name: string
    provider: string
    link_url: string | null
    placement: 'middle-two' | 'sidebar-five' | 'sidebar-four',
}

const middleTwoAds = ref<Advertisement[]>([])
const sideFourAds = ref<Advertisement[]>([])
const sideFiveAds = ref<Advertisement[]>([])

const fetchAds = async () => {
    try {
        const [middleTwoRes, sidebarFiveRes, sidebarFourRes] = await Promise.all([
            api.get('/front-ads', { params: { placement: 'middle-two', limit: 1 } }),
            api.get('/front-ads', { params: { placement: 'sidebar-five', limit: 1 } }),
            api.get('/front-ads', { params: { placement: 'sidebar-four', limit: 1 } })
            
        ])

        middleTwoAds.value = middleTwoRes.data.data ?? []
        sideFiveAds.value = sidebarFiveRes.data.data ?? []
        sideFourAds.value = sidebarFourRes.data.data ?? []
        


    } catch (error) {
        console.error('Failed to load advertisements:', error)
    }
}

const trackClick = async (ad: Advertisement) => {
    if (!ad.link_url) return
    try {
        await api.post(`/front-ads/${ad.id}/click`)
    } catch { /* non-blocking */ }
    window.open(ad.link_url, '_blank')
}

onMounted(() => {
    fetchArticles()
    fetchAds()
})
</script>