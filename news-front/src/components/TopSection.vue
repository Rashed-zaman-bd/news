<template>
  <div class="max-w-7xl mx-auto pl-4 pr-4 bg-white text-gray-900 pb-4">
    <!-- Top advert banner -->
    <div v-if="topAds[0]"
      class="w-full border-y border-gray-200 py-2 mt-20 sm:mt-0 mb-5 flex flex-col items-center cursor-pointer"
      @click="trackClick(topAds[0])">
      <img :src="topAds[0].image" :alt="topAds[0].name" class="w-full max-w-[728px] h-auto object-contain" />
      <span class="text-[10px] text-gray-400 mt-1">বিজ্ঞাপন — </span>
    </div>

    <!-- Main Section (Lead + Right Column) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pb-6">

      <!-- Main Lead Article (Left / Center Column - 7 Cols) -->

      <article v-if="featuredArticle"
        class="lg:col-span-7 flex flex-col space-y-4 border-r-0 lg:border-r border-gray-200 pr-0 lg:pr-6">
        <router-link :to="`/article/${featuredArticle.slug}`">
          <!-- Featured Image -->
          <div class="relative w-full rounded overflow-hidden bg-gray-100 border border-gray-200">
            <img :src="featuredArticle.featured_image || '/images/topimage.png'" :alt="featuredArticle.title"
              class="w-full aspect-video object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
          </div>

          <div class="flex flex-row mb-4 mt-1">
            <p v-if="featuredArticle.image_title" class="text-sm md:text-base leading-tight text-gray-900">
              {{ featuredArticle.image_title }}
            </p>
            <span class="text-red-500 pl-3">|</span>
            <p v-if="featuredArticle.image_author" class="text-sm md:text-base leading-tight text-gray-900 pl-2">
              ছবি: {{ featuredArticle.image_author }}
            </p>
          </div>

          <div>
            <h4 v-if="featuredArticle.sub_title" class="text-sm md:text-xl font-bold leading-tight text-gray-900 pb-1">
              {{ featuredArticle.sub_title }}
            </h4>

            <h2 class="text-xl md:text-3xl font-bold leading-tight text-amber-700 cursor-pointer">
              {{ featuredArticle.title }}
            </h2>

            <p v-if="featuredArticle.excerpt" class="text-gray-600 text-sm leading-relaxed pt-1">
              {{ featuredArticle.excerpt }}
            </p>
          </div>
        </router-link>
      </article>


      <!-- Right Column Grid (5 Cols split into 2 Sub-Columns) -->
      <div class="lg:col-span-5 space-y-4">

        <!-- Top Sidebar Banner Advertisement -->
        <div v-if="topSideBarAds[0]" class="w-full flex flex-col items-center cursor-pointer"
          @click="trackClick(topSideBarAds[0])">
          <img :src="topSideBarAds[0].image" :alt="topSideBarAds[0].name"
            class="w-full max-w-[728px] h-auto object-contain" />

        </div>

        <!-- 2-Column News Feed Below Banner -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Breaking News -->
            <div class="space-y-4">

              <div v-for="article in breakingArticles.slice(0, 3)" :key="article.id"
  class="border-b border-gray-200 pb-3">

  <!-- Featured + Breaking Article: shown with image -->
  <router-link v-if="article.is_featured && article.is_breaking" :to="`/article/${article.slug}`" class="block">
    <div class="aspect-video w-full overflow-hidden rounded bg-gray-100">
      <img :src="article.featured_image || '/images/topsideimage.png'" :alt="article.title"
        class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
    </div>

    <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer mt-2">
      {{ article.title }}
    </h3>
  </router-link>

  <!-- Other Breaking Articles: title only -->
  <router-link v-else :to="`/article/${article.slug}`" class="block">
    <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
      {{ article.title }}
    </h3>
    <p class="text-sm text-gray-600 pt-2">
      {{ article.excerpt }}
    </p>
  </router-link>

</div>

            </div>

            <!-- News Column B (With Ads) -->
            <div class="space-y-4">
              <div v-if="sideBarTwoAds[0]" class="w-full flex flex-col items-center cursor-pointer"
                @click="trackClick(sideBarTwoAds[0])">
                <img :src="sideBarTwoAds[0].image" :alt="sideBarTwoAds[0].name"
                  class="w-full max-w-[728px] h-auto object-contain" />
              </div>

              <div v-if="sideBarThreeAds[0]" class="w-full flex flex-col items-center cursor-pointer"
                @click="trackClick(sideBarThreeAds[0])">
                <img :src="sideBarThreeAds[0].image" :alt="sideBarThreeAds[0].name"
                  class="w-full max-w-[728px] h-auto object-contain" />
              </div>
            </div>

          </div>

      </div>

    </div>

    <!-- Bottom Section: 4-Column Grid Row (Image + Title) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 border-b border-gray-200 pb-6">

      <router-link v-for="article in bottomArticles" :key="article.id" :to="`/article/${article.slug}`"
        class="flex flex-row items-start gap-3 border-b sm:border-b-0 border-gray-200 pb-4 sm:pb-0">

        <div class="w-3/5 aspect-video shrink-0 overflow-hidden rounded border-2 border-gray-400 bg-gray-100">
          <img :src="article.featured_image || '/images/topsideimage.png'" :alt="article.title"
            class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
        </div>

        <h3 class="w-2/5 font-bold text-sm md:text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
          {{ article.title }}
        </h3>

      </router-link>

    </div>

    <!-- Middle banner -->
    <div v-if="middleAds[0]" class="w-full flex items-center justify-center cursor-pointer p-4 border-b border-gray-200"
      @click="trackClick(middleAds[0])">
      <img :src="middleAds[0].image" :alt="middleAds[0].name" class="w-full max-w-[728px] h-auto object-contain" />

    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
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
  image_title: string
  image_author: string

  category?: any
  sub_category?: any
  author?: {
    id: number
    name: string
  }
  editor?: {
    id: number
    name: string
  }
}

const featuredArticle = ref<Article | null>(null)
const breakingArticles = ref<Article[]>([])
const articles = ref<Article[]>([])

const fetchArticles = async () => {
  try {
    const [
      featuredRes,
      breakingRes,
      articlesRes
    ] = await Promise.all([
      api.get('/articles/featured'),
      api.get('/articles/breaking'),
      api.get('/articles', {
        params: {
          per_page: 10
        }
      })
    ])

    featuredArticle.value = featuredRes.data.data ?? null
    breakingArticles.value = breakingRes.data.data ?? []
    articles.value = articlesRes.data.data ?? []

  } catch (error) {
    console.error('Failed to load articles:', error)
  }
}

const bottomArticles = computed(() => {
  const breakingIds = new Set(
    breakingArticles.value.map(article => article.id)
  )

  return articles.value
    .filter(article => {
      return (
        article.id !== featuredArticle.value?.id &&
        !breakingIds.has(article.id)
      )
    })
    .slice(0, 4)
})


interface Advertisement {
  id: number
  image: string
  name: string
  provider: string
  link_url: string | null
  placement: 'top' | 'middle' | 'middle-two' | 'middle-three' | 'middle-four' | 'middle-five' | 'middle-six' | 'middle-seven' | 'middle-eight' | 'middle-nine' | 'middle-ten' | 'sidebar' | 'sidebar-two' | 'sidebar-three' | 'sidebar-four' | 'sidebar-five' | 'sidebar-six',
}

const topAds = ref<Advertisement[]>([])
const middleAds = ref<Advertisement[]>([])
const middleTwoAds = ref<Advertisement[]>([])
const middleThreeAds = ref<Advertisement[]>([])
const topSideBarAds = ref<Advertisement[]>([])
const sideBarTwoAds = ref<Advertisement[]>([])
const sideBarThreeAds = ref<Advertisement[]>([])
const sidebarTwoAds = ref<Advertisement[]>([])

const fetchAds = async () => {
  try {
    const [topRes, middleRes, middleTwoRes, middleThreeRes, sidebarRes, sidebarTwoRes, sidebarThreeRes] = await Promise.all([
      api.get('/front-ads', { params: { placement: 'top', limit: 1 } }),
      api.get('/front-ads', { params: { placement: 'middle', limit: 1 } }),
      api.get('/advertisements', { params: { placement: 'middle-two', limit: 1 } }),
      api.get('/advertisements', { params: { placement: 'middle-three', limit: 1 } }),
      api.get('/front-ads', { params: { placement: 'sidebar', limit: 1 } }),
      api.get('/front-ads', { params: { placement: 'sidebar-two', limit: 1 } }),
      api.get('/front-ads', { params: { placement: 'sidebar-three', limit: 1 } }),
    ])
    topAds.value = topRes.data.data ?? []
    middleAds.value = middleRes.data.data ?? []
    middleTwoAds.value = middleTwoRes.data.data ?? []
    middleThreeAds.value = middleThreeRes.data.data ?? []
    topSideBarAds.value = sidebarRes.data.data ?? []
    sideBarTwoAds.value = sidebarTwoRes.data.data ?? []
    sideBarThreeAds.value = sidebarThreeRes.data.data ?? []


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
  fetchAds()
  fetchArticles()
})
</script>