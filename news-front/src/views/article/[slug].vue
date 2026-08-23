<template>
    <div class="max-w-7xl mx-auto px-4 py-6 text-gray-900">

        <!-- Loading -->
        <div v-if="loading" class="text-center py-20 text-gray-400">
            <i class="bi bi-arrow-repeat animate-spin text-3xl"></i>
        </div>

        <!-- Article -->
        <template v-else-if="article">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- =====================================================
                     LEFT / MAIN ARTICLE
                ====================================================== -->
                <main class="lg:col-span-9">

                    <!-- Category -->
                    <div class="flex items-center gap-2 text-sm sm:text-base mb-3">

                        <i class="bi bi-file-earmark-ruled text-red-600"></i>

                        <router-link
                            v-if="article.category"
                            :to="`/category/${article.category.slug}`"
                            class="text-red-600 font-semibold hover:underline"
                        >
                            {{ article.category.name }}
                        </router-link>

                        <span
                            v-if="article.sub_category"
                            class="text-gray-400"
                        >
                            ›
                        </span>

                        <router-link
                            v-if="article.sub_category"
                            :to="`/category/${article.sub_category.slug}`"
                            class="text-gray-500 hover:underline"
                        >
                            {{ article.sub_category.name }}
                        </router-link>

                    </div>


                    <!-- =================================================
                         TITLE
                    ================================================== -->
                    <h1
                        class="text-3xl sm:text-4xl lg:text-[40px]
                               font-bold text-gray-900
                               leading-tight mb-4"
                    >
                        {{ article.title }}
                    </h1>


                    <!-- =================================================
                         AUTHOR + DATE
                    ================================================== -->
                    <div class="mb-4">

                        <div class="text-base font-medium text-gray-800 mb-1">
                            খবরজোন অনলাইন
                        </div>

                        <div class="text-sm text-gray-500">
                            <span class="font-medium">
                                প্রকাশ :
                            </span>

                            {{ formatBanglaDate(article.published_at) }}
                        </div>

                    </div>


                    <!-- =================================================
                         TOP BANNER
                    ================================================== -->
                    <div
                        class="w-full border-y border-gray-200
                               py-2 mb-3 flex justify-center"
                    >
                        <img
                            src="/images/topbanner.png"
                            alt="Advertisement"
                            class="w-full max-w-[728px] h-auto object-contain"
                        />
                    </div>


                    <!-- =================================================
                         SHARE BAR
                    ================================================== -->
                    <div
                        class="flex items-center justify-between
                               border-b border-gray-200
                               pb-2 mb-3"
                    >

                        <!-- Social -->
                        <div class="flex items-center gap-2">

                            <button
                                class="w-8 h-8 rounded-full bg-gray-100
                                       flex items-center justify-center
                                       text-gray-500 hover:bg-gray-200"
                            >
                                <i class="bi bi-facebook"></i>
                            </button>

                            <button
                                class="w-8 h-8 rounded-full bg-gray-100
                                       flex items-center justify-center
                                       text-gray-500 hover:bg-gray-200"
                            >
                                <i class="bi bi-twitter-x"></i>
                            </button>

                            <button
                                class="w-8 h-8 rounded-full bg-gray-100
                                       flex items-center justify-center
                                       text-gray-500 hover:bg-gray-200"
                            >
                                <i class="bi bi-whatsapp"></i>
                            </button>

                            <button
                                class="w-8 h-8 rounded-full bg-gray-100
                                       flex items-center justify-center
                                       text-gray-500 hover:bg-gray-200"
                            >
                                <i class="bi bi-telegram"></i>
                            </button>

                            <button
                                class="w-8 h-8 rounded-full bg-gray-100
                                       flex items-center justify-center
                                       text-gray-500 hover:bg-gray-200"
                            >
                                <i class="bi bi-linkedin"></i>
                            </button>

                            <button
                                class="w-8 h-8 rounded-full bg-gray-100
                                       flex items-center justify-center
                                       text-gray-500 hover:bg-gray-200"
                            >
                                <i class="bi bi-envelope-fill"></i>
                            </button>

                        </div>

                    </div>


                    <!-- =================================================
                         FEATURED IMAGE
                    ================================================== -->
                    <div
                        v-if="article.featured_image"
                        class="w-full overflow-hidden mb-1"
                    >

                        <img
                            :src="article.featured_image"
                            :alt="article.title"
                            class="w-full h-auto object-cover"
                        />

                    </div>


                    <!-- Image Caption -->
                    <div
                        v-if="article.featured_image"
                        class="text-xs text-gray-500
                               border-b border-gray-200
                               pb-2 mb-5"
                    >
                        {{ article.title }}
                    </div>


                    <div class="max-w-2xl mx-auto">
    <!-- =================================================
         CONTENT TOP AD
    ================================================== -->
    <div class="w-full flex justify-center py-3 mb-5">
        <img
            src="/images/topbanner.png"
            alt="Advertisement"
            class="max-w-full h-auto"
        />
    </div>

    <!-- =================================================
         FIRST PART OF CONTENT
    ================================================== -->
    <article
        class="prose prose-lg max-w-none text-gray-800 leading-relaxed
               prose-headings:text-gray-900
               prose-p:mb-5
               prose-img:w-full
               prose-img:h-auto
               prose-a:text-red-600
               prose-a:no-underline
               hover:prose-a:underline

               [&>p:first-child]:bg-gray-50
               [&>p:first-child]:border-l-4
               [&>p:first-child]:border-red-500
               [&>p:first-child]:px-4
               [&>p:first-child]:py-3"
        v-html="firstContent"
    ></article>

    <!-- =================================================
         MIDDLE AD (only if there's more content after)
    ================================================== -->
    <div
        v-if="secondContent"
        class="w-full flex justify-center py-6 my-4 border-y border-gray-100"
    >
        <img
            src="/images/topbanner.png"
            alt="Advertisement"
            class="max-w-full h-auto"
        />
    </div>

    <!-- =================================================
         REMAINING CONTENT
    ================================================== -->
    <article
        v-if="secondContent"
        class="prose prose-lg max-w-none text-gray-800 leading-relaxed
               prose-headings:text-gray-900
               prose-p:mb-5
               prose-img:w-full
               prose-img:h-auto
               prose-a:text-red-600
               prose-a:no-underline
               hover:prose-a:underline"
        v-html="secondContent"
    ></article>
</div>


                    <!-- =================================================
                         RELATED ARTICLES
                    ================================================== -->
                    <div
                        v-if="related.length"
                        class="mt-12 pt-6 border-t border-gray-200"
                    >

                        <h2
                            class="text-xl font-bold
                                   text-red-600 mb-5"
                        >
                            সম্পর্কিত সংবাদ
                        </h2>

                        <div
                            class="grid grid-cols-1
                                   sm:grid-cols-2
                                   lg:grid-cols-3 gap-5"
                        >

                            <router-link
                                v-for="item in related"
                                :key="item.id"
                                :to="`/article/${item.slug}`"
                                class="group"
                            >

                                <div
                                    class="relative
                                           h-44
                                           overflow-hidden
                                           rounded"
                                >

                                    <img
                                        v-if="item.featured_image"
                                        :src="item.featured_image"
                                        :alt="item.title"
                                        class="w-full h-full object-cover
                                               group-hover:scale-105
                                               transition duration-300"
                                    />

                                    <div
                                        v-else
                                        class="w-full h-full
                                               bg-gray-200"
                                    ></div>

                                </div>

                                <h3
                                    class="mt-2 font-bold
                                           text-sm leading-snug
                                           group-hover:text-red-600"
                                >
                                    {{ item.title }}
                                </h3>

                            </router-link>

                        </div>

                    </div>

                </main>


                <!-- =====================================================
                     RIGHT SIDEBAR
                ====================================================== -->
                <aside class="lg:col-span-3">

                    <div class="lg:sticky lg:top-20">

                        <!-- Latest News -->
                        <div class="mb-5">

                            <h2
                                class="text-sm font-bold
                                       text-gray-600
                                       border-b border-gray-300
                                       pb-2 mb-3"
                            >
                                সর্বশেষ খবর
                            </h2>

                            <div
                                v-for="item in related.slice(0, 5)"
                                :key="item.id"
                                class="flex gap-3 py-3
                                       border-b border-gray-200"
                            >

                                <router-link
                                    :to="`/article/${item.slug}`"
                                    class="flex gap-3 w-full"
                                >

                                    <div
                                        class="w-20 h-14 flex-shrink-0
                                               overflow-hidden"
                                    >
                                        <img
                                            v-if="item.featured_image"
                                            :src="item.featured_image"
                                            :alt="item.title"
                                            class="w-full h-full object-cover"
                                        />
                                    </div>

                                    <div>
                                        <h3
                                            class="text-xs
                                                   font-semibold
                                                   leading-relaxed
                                                   text-gray-800
                                                   hover:text-red-600"
                                        >
                                            {{ item.title }}
                                        </h3>
                                    </div>

                                </router-link>

                            </div>

                        </div>


                        <!-- Sidebar Advertisement -->
                        <div class="space-y-5">

                            <div class="overflow-hidden">
                                <img
                                    src="/images/topbanner.png"
                                    alt="Advertisement"
                                    class="w-full h-auto"
                                />
                            </div>

                            <div class="bg-gray-50 border border-gray-200 p-3">

                                <h3
                                    class="text-sm font-semibold
                                           text-red-600 mb-2"
                                >
                                    বিশেষ সংবাদ
                                </h3>

                                <p
                                    class="text-xs
                                           text-gray-700
                                           leading-relaxed"
                                >
                                    সর্বশেষ সংবাদ ও গুরুত্বপূর্ণ
                                    আপডেট জানতে খবরজোনের সাথে থাকুন।
                                </p>

                            </div>

                            <div class="overflow-hidden">
                                <img
                                    src="/images/topbanner.png"
                                    alt="Advertisement"
                                    class="w-full h-auto"
                                />
                            </div>

                        </div>

                    </div>

                </aside>

            </div>

        </template>


        <!-- No article -->
        <div
            v-else
            class="text-center py-20 text-gray-400"
        >
            আর্টিকেল পাওয়া যায়নি
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
    content?: string
    excerpt?: string | null
    featured_image: string | null
    published_at: string | null

    category?: {
        id: number
        name: string
        slug: string
    }

    sub_category?: {
        id: number
        name: string
        slug: string
    } | null

    author?: {
        id: number
        name: string
    }
}

const route = useRoute()

const article = ref<Article | null>(null)
const related = ref<Article[]>([])
const loading = ref(false)


const formatBanglaDate = (date: string | null) => {

    if (!date) {
        return ''
    }

    const d = new Date(date)

    const datePart = new Intl.DateTimeFormat('bn-BD', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
        .format(d)
        .replace(/,/g, '')
        .replace(/ এ$/, '')

    const timePart = new Intl.DateTimeFormat('bn-BD', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).format(d)

    return `${datePart} ; ${timePart} মি:`
}


const fetchArticle = async () => {
    loading.value = true
    article.value = null

    try {
        const slug = route.params.slug as string
        const { data } = await api.get(`/articles/${slug}`)

        article.value = data.data
        related.value = data.related?.data ?? []

        console.log('content:', article.value?.content) // <-- temp debug

    } catch (error) {
        console.error('Failed to load article:', error)
    } finally {
        loading.value = false
        window.scrollTo({ top: 0, behavior: 'auto' })
    }
}

const parsedContent = computed(() => {
    if (!article.value?.content) {
        return { first: '', second: '' }
    }

    const parser = new DOMParser()
    const doc = parser.parseFromString(article.value.content, 'text/html')

    let blocks = Array.from(doc.body.children)

    // If the editor wrapped everything in one container div,
    // unwrap it so we split its children instead of the whole thing
    if (blocks.length === 1 && blocks[0].children.length > 1) {
        blocks = Array.from(blocks[0].children)
    }

    // Fallback: no block-level elements at all (plain text / inline only)
    if (blocks.length === 0) {
        return { first: article.value.content, second: '' }
    }

    const first = blocks.slice(0, 10).map(el => el.outerHTML).join('')
    const second = blocks.slice(10).map(el => el.outerHTML).join('')

    return { first, second }
})

const firstContent = computed(() => parsedContent.value.first)
const secondContent = computed(() => parsedContent.value.second)


watch(
    () => route.params.slug,
    fetchArticle
)

onMounted(fetchArticle)
</script>