//views/category/[slug].vue
<template>
    <div class="max-w-7xl mx-auto px-4 py-6 text-gray-900">

        <!-- Top advert banner -->
        <div class="w-full flex items-center justify-center border-b cursor-pointer p-4 mt-14 sm:mt-0">
            <img src="/images/topbanner.png" alt="Top Banner" class="max-w-full h-auto" />
        </div>

        <!-- Category header -->
        <h1 class="text-3xl sm:text-4xl font-bold text-red-600 mt-6 sm:mt-10 mb-4">
            {{ category?.name ?? '...' }}
        </h1>

        <!-- All Sub Category Names (Filter Chips) -->
        <div v-if="category?.children?.length" class="flex flex-wrap gap-2 sm:gap-3 mb-6">
            <button
                type="button"
                class="text-sm sm:text-base font-semibold px-3 py-1.5 rounded-full transition-colors cursor-pointer"
                :class="activeSubCategory === null
                    ? ' text-gray-600'
                    : ''"
                @click="filterBySubCategory(null)"
            >
                সকল
            </button>
            <button
                v-for="sub in category.children"
                :key="sub.id"
                type="button"
                class="text-sm sm:text-base font-semibold px-3 py-1.5 rounded-full transition-colors cursor-pointer"
                :class="activeSubCategory === sub.slug
                    ? 'text-gray-600'
                    : ''"
                @click="filterBySubCategory(sub.slug)"
            >
                {{ sub.name }}
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT SECTION: Main Content Area -->
            <div class="lg:col-span-8 xl:col-span-9 flex flex-col gap-6">

                <!-- TOP ROW: Lead Article + Secondary Article (Articles #1 & #2) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pb-6 border-b border-gray-200">
                    
                    <!-- Lead Article (#1) -->
                    <router-link
                        v-if="leadArticle"
                        :to="`/article/${leadArticle.slug}`"
                        class="sm:col-span-2 group block relative rounded overflow-hidden bg-black h-64 sm:h-80 md:h-96"
                    >
                        <img
                            v-if="leadArticle.featured_image"
                            :src="leadArticle.featured_image"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-90"
                            alt=""
                        />
                        <div v-else class="w-full h-full bg-gray-800"></div>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent p-4 sm:p-5 flex flex-col justify-end">
                            <h2 class="text-white font-bold text-lg sm:text-xl md:text-2xl leading-snug group-hover:underline">
                                {{ leadArticle.title }}
                            </h2>
                            <span class="text-xs text-gray-300 mt-2">
                                {{ timeAgo(leadArticle.published_at) }}
                            </span>
                        </div>
                    </router-link>

                    <!-- Secondary Article (#2) -->
                    <router-link
                        v-if="secondaryArticle"
                        :to="`/article/${secondaryArticle.slug}`"
                        class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-0 group"
                    >
                        <div class="relative h-28 sm:h-44 w-full rounded overflow-hidden sm:mb-2">
                            <img
                                v-if="secondaryArticle.featured_image"
                                :src="secondaryArticle.featured_image"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                alt=""
                            />
                            <div v-else class="w-full h-full bg-gray-200"></div>
                        </div>

                        <div class="flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-base sm:text-xl text-gray-900 group-hover:text-red-700 leading-snug">
                                    {{ secondaryArticle.title }}
                                </h3>
                                <p v-if="secondaryArticle.excerpt" class="text-sm sm:text-base text-gray-600 mt-1 line-clamp-2 sm:line-clamp-3 leading-relaxed">
                                    {{ secondaryArticle.excerpt }}
                                </p>
                            </div>
                            <span class="text-[14px] text-gray-600 mt-2">
                                {{ timeAgo(secondaryArticle.published_at) }}
                            </span>
                        </div>
                    </router-link>
                </div>

                <!-- TOP SECTION GRID: Next 3 Articles (#3, #4, #5) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-5">
                    <router-link
                        v-for="article in topGridArticles"
                        :key="article.id"
                        :to="`/article/${article.slug}`"
                        class="group flex flex-col justify-between"
                    >
                        <div>
                            <div class="relative h-28 sm:h-36 w-full rounded overflow-hidden mb-2">
                                <img
                                    v-if="article.featured_image"
                                    :src="article.featured_image"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    alt=""
                                />
                                <div v-else class="w-full h-full bg-gray-200"></div>
                            </div>

                            <h4 class="font-bold text-base sm:text-xl text-gray-900 leading-snug group-hover:text-red-700">
                                <span v-if="article.sub_category" class="text-red-600 font-bold">
                                    {{ article.sub_category.name }} • 
                                </span>
                                {{ article.title }}
                            </h4>
                            <p v-if="article.excerpt" class="text-sm sm:text-base text-gray-700 mt-3 line-clamp-2 sm:line-clamp-3 leading-relaxed">
                                {{ article.excerpt }}
                            </p>
                        </div>

                        <span class="text-[14px] text-gray-600 mt-4">
                            {{ timeAgo(article.published_at) }}
                        </span>
                    </router-link>
                </div>

            </div>

            <!-- RIGHT SECTION: Sidebar -->
            <div class="lg:col-span-4 xl:col-span-3 flex flex-col gap-6 border-l border-gray-300 p-3">

                <!-- Side Banner Slot -->
                <div class="w-full border rounded overflow-hidden flex justify-center">
                    <img src="/images/topbanner.png" alt="Ad" class="w-full h-auto object-cover max-w-xs sm:max-w-none" />
                </div>

                <!-- Tabbed Widget -->
                <div class="border-t-2 border-red-600 pt-2">
                    <div class="flex border-b border-gray-200 gap-6 text-lg font-bold pb-2">
                        <button
                            class="relative pb-1 transition-colors"
                            :class="sideTab === 'popular' ? 'text-gray-900 font-extrabold after:absolute after:bottom-[-9px] after:left-0 after:right-0 after:h-[2px] after:bg-red-600' : 'text-gray-500 hover:text-gray-800'"
                            @click="sideTab = 'popular'"
                        >
                            সর্বাধিক পঠিত
                        </button>
                        <button
                            class="relative pb-1 transition-colors"
                            :class="sideTab === 'featured' ? 'text-gray-900 font-extrabold after:absolute after:bottom-[-9px] after:left-0 after:right-0 after:h-[2px] after:bg-red-600' : 'text-gray-500 hover:text-gray-800'"
                            @click="sideTab = 'featured'"
                        >
                            নির্বাচিত
                        </button>
                        
                    </div>

                    <!-- Numbered List -->
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

        <!-- Middle Advert Banner (Separator) -->
        <div class="my-8 pt-4 border-t border-gray-200 flex flex-col items-center">
            <span class="text-[10px] text-gray-400 mb-1">বিজ্ঞাপন</span>
            <div class="w-full max-w-4xl bg-amber-100 rounded flex items-center justify-center py-2">
                <img src="/images/top-advertis.png" alt="Bottom Banner" class="max-w-full h-auto" />
            </div>
        </div>

        <!-- BOTTOM SECTION: Remaining 10 Articles (#6 to #15) -->
        <div v-if="bottomArticles.length" class="mt-8 max-w-lg mx-auto ">
            <div class="grid grid-cols-1 gap-4 sm:gap-6 justify-center">
                <template v-for="(chunk, chunkIndex) in listArticleChunks" :key="`chunk-${chunkIndex}`">
                    <div class="flex flex-col gap-4">
                        <router-link
                            v-for="article in chunk"
                            :key="article.id"
                            :to="`/article/${article.slug}`"
                            class="group flex flex-row md:flex-col items-start gap-3 sm:gap-4 pb-3 border-b border-gray-100 last:border-0"
                        >
                            <!-- Image Container (Fixed dimensions on mobile for horizontal alignment) -->
                            <div class="flex gap-3 group">

                                <!-- Image -->
                                <div class="relative h-20 w-28 sm:h-46 sm:w-56 shrink-0 rounded overflow-hidden">
                                    <img
                                        v-if="article.featured_image"
                                        :src="article.featured_image"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        :alt="article.title"
                                    />
                                    <div v-else class="w-full h-full bg-gray-200"></div>

                                    <div v-if="article.is_video" class="absolute top-1 left-1 bg-red-600 text-white p-0.5 rounded">
                                        <i class="bi bi-camera-fill text-[10px]"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-sm sm:text-base text-gray-900 leading-snug group-hover:text-red-700">
                                        <span v-if="article.sub_category" class="text-red-600 font-bold">
                                            {{ article.sub_category.name }} •
                                        </span>
                                        {{ article.title }}
                                    </h4>
                                    <p v-if="article.excerpt" class="text-base text-gray-500 mt-1 leading-relaxed">
                                        {{ article.excerpt }}
                                    </p>
                                    <span class="text-[11px] text-gray-400 mt-1.5 block">
                                    {{ timeAgo(article.published_at) }}
                                </span>
                                </div>

                            </div>
                        </router-link>
                    </div>
                </template>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';

import {  useRouter } from 'vue-router';

const router = useRouter();

interface Category {
    id: number;
    name: string;
    slug: string;
    children?: Category[];
}

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

const route = useRoute();

const category = ref<Category | null>(null);
const articles = ref<Article[]>([]);
const featuredList = ref<Article[]>([]);
const popularList = ref<Article[]>([]);
const loading = ref(false);
const sideTab = ref<'featured' | 'popular'>('popular')
const meta = reactive({ current_page: 1, last_page: 1 });

// Article Distribution Strategy (Total 15)
// Top Section: 5 Articles total
const leadArticle = computed(() => articles.value[0] ?? null);
const secondaryArticle = computed(() => articles.value[1] ?? null);
const topGridArticles = computed(() => articles.value.slice(2, 5));

// Bottom Section: Next 10 Articles (#6 to #15)
const bottomArticles = computed(() => articles.value.slice(5, 15));

// Split bottom 10 articles into 2 column chunks (5 per column)
const listArticleChunks = computed(() => {
    const list = bottomArticles.value;
    const chunkSize = Math.ceil(list.length / 2);
    const chunks: Article[][] = [];
    for (let i = 0; i < list.length; i += chunkSize) {
        chunks.push(list.slice(i, i + chunkSize));
    }
    return chunks;
});

const sidePanelList = computed(() =>
    (sideTab.value === 'featured' ? featuredList.value : popularList.value).slice(0, 8)
);

const toBengaliNumber = (num: number | string) => {
    const bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return num.toString().replace(/\d/g, (digit) => bengaliDigits[parseInt(digit)]);
};

const timeAgo = (dateStr: string | null) => {
    if (!dateStr) return '';
    const diffMs = Date.now() - new Date(dateStr).getTime();
    const mins = Math.floor(diffMs / 60000);
    if (mins < 1) return 'এইমাত্র';
    if (mins < 60) return `${toBengaliNumber(mins)} মিনিট আগে`;
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${toBengaliNumber(hours)} ঘণ্টা আগে`;
    const days = Math.floor(hours / 24);
    return `${toBengaliNumber(days)} দিন আগে`;
};

const activeSubCategory = ref<string | null>((route.query.sub as string) ?? null);

const filterBySubCategory = (subSlug: string | null) => {
    if (activeSubCategory.value === subSlug) return; // no-op if already active
    activeSubCategory.value = subSlug;

    // Reflect the filter in the URL (shareable/bookmarkable, browser back works)
    router.replace({
        query: { ...route.query, sub: subSlug ?? undefined },
    });

    fetchArticles(1);
};

const fetchArticles = async (page = 1) => {
    loading.value = true;
    try {
        const slug = route.params.slug as string;

        const { data } = await api.get(`/categories/${slug}/articles`, {
            params: {
                page,
                per_page: 15,
                sub_category: activeSubCategory.value ?? undefined,
            },
        });

        articles.value = data.data ?? [];
        category.value = data.category ?? category.value;
        meta.current_page = data.meta?.current_page ?? 1;
        meta.last_page = data.meta?.last_page ?? 1;

        featuredList.value = articles.value.filter((a) => a.is_featured);

        const { data: popularData } = await api.get(`/categories/${slug}/popular`);
        popularList.value = popularData.data ?? [];
    } catch (error) {
        console.error('Failed to load category articles:', error);
        articles.value = [];
    } finally {
        loading.value = false;
    }
};

watch(
    () => route.params.slug,
    () => {
        activeSubCategory.value = null; 
        fetchArticles(1);
    }
);

onMounted(() => fetchArticles());

watch(
    () => route.params.slug,
    () => fetchArticles(1)
);

onMounted(() => fetchArticles());
</script>