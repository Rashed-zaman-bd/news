<template>
    <div class="max-w-7xl mx-auto px-4 py-6">

        <!-- Top advert banner -->
        <div class="w-full flex items-center justify-center border-b cursor-pointer p-4 mt-14 sm:mt-0">
        <img src="/images/topbanner.png" alt="Top Banner" />
        </div>
        <!-- Category header -->
        <h1 class="text-4xl font-bold text-red-600 mt-10 mb-4">
            {{ category?.name ?? '...' }}
        </h1>

        <!-- All Sub Category Names -->
        <div v-if="category?.children?.length" class="flex flex-wrap gap-3 mb-6">
            <router-link
                v-for="sub in category.children"
                :key="sub.id"
                :to="`/category/${sub.slug}`"
                class="text-base font-semibold text-gray-600 hover:text-red-600 px-3 py-1.5 transition-colors"
            >
                {{ sub.name }}
            </router-link>
        </div>

        <div v-if="loading" class="text-center py-16 text-gray-400">
            <i class="bi bi-arrow-repeat animate-spin text-2xl"></i>
        </div>

        <template v-else>
            <!-- Top section: lead + secondary + side widget -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Lead article -->
                <router-link
                    v-if="leadArticle"
                    :to="`/article/${leadArticle.slug}`"
                    class="lg:col-span-1 block group"
                >
                    <div class="relative rounded-lg overflow-hidden">
                        <img
                            v-if="leadArticle.featured_image"
                            :src="leadArticle.featured_image"
                            class="w-full h-64 object-cover"
                            alt=""
                        >
                        <div v-else class="w-full h-64 bg-gray-200"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                            <h2 class="text-white font-bold text-lg leading-snug group-hover:underline">
                                {{ leadArticle.title }}
                            </h2>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">{{ timeAgo(leadArticle.published_at) }}</p>
                </router-link>

                <!-- Secondary article -->
                <router-link
                    v-if="secondaryArticle"
                    :to="`/article/${secondaryArticle.slug}`"
                    class="lg:col-span-1 block group"
                >
                    <div class="relative rounded-lg overflow-hidden">
                        <img
                            v-if="secondaryArticle.featured_image"
                            :src="secondaryArticle.featured_image"
                            class="w-full h-64 object-cover"
                            alt=""
                        >
                        <div v-else class="w-full h-64 bg-gray-200"></div>
                        <div
                            v-if="secondaryArticle.is_video"
                            class="absolute inset-0 flex items-center justify-center bg-black/20"
                        >
                            <i class="bi bi-play-circle-fill text-white text-5xl drop-shadow"></i>
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-800 mt-2 group-hover:text-red-600">
                        {{ secondaryArticle.title }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">{{ timeAgo(secondaryArticle.published_at) }}</p>
                </router-link>

                <!-- Side: ad slot + featured/popular tabs -->
                <div class="lg:col-span-1 flex flex-col gap-4">
                    <div class="bg-gray-100 rounded-lg h-40 flex items-center justify-center text-gray-400 text-sm">
                        বিজ্ঞাপন
                    </div>

                    <div class="border rounded-lg">
                        <div class="flex border-b text-sm font-medium">
                            <button
                                class="flex-1 py-2 text-center"
                                :class="sideTab === 'featured' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500'"
                                @click="sideTab = 'featured'"
                            >
                                নির্বাচিত
                            </button>
                            <button
                                class="flex-1 py-2 text-center"
                                :class="sideTab === 'popular' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500'"
                                @click="sideTab = 'popular'"
                            >
                                পঠিত
                            </button>
                        </div>
                        <ul class="divide-y">
                            <li
                                v-for="(item, i) in sidePanelList"
                                :key="item.id"
                                class="flex gap-3 px-3 py-3"
                            >
                                <span class="text-gray-300 font-bold text-lg w-5 shrink-0">{{ i + 1 }}</span>
                                <router-link
                                    :to="`/article/${item.slug}`"
                                    class="text-sm text-gray-700 hover:text-red-600 leading-snug"
                                >
                                    {{ item.title }}
                                </router-link>
                            </li>
                            <li v-if="!sidePanelList.length" class="px-3 py-4 text-sm text-gray-400">
                                কোনো তথ্য নেই
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Article grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <router-link
                    v-for="article in gridArticles"
                    :key="article.id"
                    :to="`/article/${article.slug}`"
                    class="flex flex-col"
                >
                    <img
                        v-if="article.featured_image"
                        :src="article.featured_image"
                        class="w-full h-40 object-cover rounded-lg"
                        alt=""
                    >
                    <div v-else class="w-full h-40 bg-gray-200 rounded-lg"></div>

                    <p class="text-xs mt-2">
                        <router-link
                            v-if="article.sub_category"
                            :to="`/category/${article.sub_category.slug}`"
                            class="text-red-600 font-medium hover:underline"
                            @click.stop
                        >
                            {{ article.sub_category.name }}
                        </router-link>
                        <span class="text-gray-400"> • {{ timeAgo(article.published_at) }}</span>
                    </p>

                    <h3 class="font-semibold text-gray-800 mt-1 leading-snug hover:text-red-600">
                        {{ article.title }}
                    </h3>
                </router-link>

                <p v-if="!gridArticles.length" class="col-span-full text-center py-10 text-gray-400">
                    কোনো আর্টিকেল পাওয়া যায়নি।
                </p>
            </div>

            <!-- Pagination -->
            <div v-if="meta.last_page > 1" class="flex justify-center items-center gap-2 mt-8">
                <button
                    :disabled="meta.current_page === 1"
                    @click="fetchArticles(meta.current_page - 1)"
                    class="px-3 py-1 rounded-md border hover:bg-gray-50 disabled:opacity-40"
                >
                    পূর্ববর্তী
                </button>
                <span class="text-sm text-gray-600">{{ meta.current_page }} / {{ meta.last_page }}</span>
                <button
                    :disabled="meta.current_page === meta.last_page"
                    @click="fetchArticles(meta.current_page + 1)"
                    class="px-3 py-1 rounded-md border hover:bg-gray-50 disabled:opacity-40"
                >
                    পরবর্তী
                </button>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';

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
const sideTab = ref<'featured' | 'popular'>('featured');
const meta = reactive({ current_page: 1, last_page: 1 });

const leadArticle = computed(() => articles.value[0] ?? null);
const secondaryArticle = computed(() => articles.value[1] ?? null);
const gridArticles = computed(() => articles.value.slice(2));

const sidePanelList = computed(() =>
    (sideTab.value === 'featured' ? featuredList.value : popularList.value).slice(0, 8)
);

const timeAgo = (dateStr: string | null) => {
    if (!dateStr) return '';
    const diffMs = Date.now() - new Date(dateStr).getTime();
    const mins = Math.floor(diffMs / 60000);
    if (mins < 1) return 'এইমাত্র';
    if (mins < 60) return `${mins} মিনিট আগে`;
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${hours} ঘণ্টা আগে`;
    const days = Math.floor(hours / 24);
    return `${days} দিন আগে`;
};

const fetchArticles = async (page = 1) => {
    loading.value = true;
    try {
        const slug = route.params.slug as string;
        const { data } = await api.get(`/categories/${slug}/articles`, { params: { page } });

        articles.value = data.data ?? [];
        category.value = data.category ?? category.value;
        meta.current_page = data.meta?.current_page ?? 1;
        meta.last_page = data.meta?.last_page ?? 1;

        featuredList.value = articles.value.filter((a) => a.is_featured);

        // fetch real "most read" separately — independent of pagination
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
    () => fetchArticles(1)
);

onMounted(() => fetchArticles());
</script>