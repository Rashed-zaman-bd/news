<template>
    <div class="p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">আর্টিকেল ব্যবস্থাপনা</h1>
            <button
                @click="openCreateModal"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition"
            >
                <i class="bi bi-plus-lg mr-1"></i> নতুন আর্টিকেল
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <select v-model="filters.status" @change="fetchArticles(1)" class="border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">সব স্ট্যাটাস</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>

                <select v-model="filters.category_id" @change="onCategoryChange" class="border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">সব ক্যাটাগরি</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                    </option>
                </select>

                <select
                    v-model="filters.sub_category_id"
                    @change="fetchArticles(1)"
                    :disabled="!filters.category_id"
                    class="border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300 disabled:bg-gray-100"
                >
                    <option value="">সব সাব-ক্যাটাগরি</option>
                    <option v-for="sub in activeSubCategories" :key="sub.id" :value="sub.id">
                        {{ sub.name }}
                    </option>
                </select>
                

                <select
                    v-model="filters.author_id"
                    @change="fetchArticles(1)"
                    class="border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                >
                    <option value="">সব লেখক</option>
                    <option v-for="a in authors" :key="a.id" :value="a.id">
                        {{ a.name }}
                    </option>
                </select>

                <input
                    v-model="filters.date_from"
                    @change="fetchArticles(1)"
                    type="date"
                    class="border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                    title="তারিখ থেকে"
                >

                <input
                    v-model="filters.date_to"
                    @change="fetchArticles(1)"
                    type="date"
                    class="border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                    title="তারিখ পর্যন্ত"
                >
            </div>

            <div class="flex flex-col sm:flex-row gap-3 mt-3">
                <input
                    v-model="filters.search"
                    @keyup.enter="fetchArticles(1)"
                    type="text"
                    placeholder="শিরোনাম দিয়ে খুঁজুন..."
                    class="border rounded-md px-3 py-2 text-sm flex-1 outline-none focus:ring-2 focus:ring-blue-300"
                >
                <div class="flex gap-2">
                    <button
                        @click="resetFilters"
                        class="px-4 py-2 rounded-md border text-sm text-gray-600 hover:bg-gray-50"
                    >
                        রিসেট
                    </button>
                    <button
                        @click="fetchArticles(1)"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm transition"
                    >
                        খুঁজুন
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-10 text-gray-500">
            <i class="bi bi-arrow-repeat animate-spin text-2xl"></i>
            <p class="mt-2">লোড হচ্ছে...</p>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">ছবি</th>
                        <th class="px-4 py-3">শিরোনাম</th>
                        <th class="px-4 py-3">ক্যাটাগরি/সাব ক্যাটাগরি</th>
                        <th class="px-4 py-3">লেখক</th>
                        <th class="px-4 py-3">স্ট্যাটাস</th>
                        <th class="px-4 py-3">ভিউ</th>
                        <th class="px-4 py-3">প্রকাশিত</th>
                        <th class="px-4 py-3 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="article in articles" :key="article.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="relative w-12 h-12">
                                <img
                                    v-if="article.featured_image"
                                    :src="article.featured_image"
                                    class="w-12 h-12 object-cover rounded"
                                    alt="thumbnail"
                                >
                                <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                    <i class="bi bi-image"></i>
                                </div>

                                <span
                                    v-if="article.images && article.images.length"
                                    class="absolute -bottom-1 -right-1 bg-blue-600 text-white text-[10px] leading-none px-1.5 py-0.5 rounded-full"
                                    :title="`${article.images.length}টি অতিরিক্ত ছবি`"
                                >
                                    +{{ article.images.length }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="font-medium text-gray-800 truncate">{{ article.title }}</p>
                            <div class="flex gap-1 mt-1">
                                <span v-if="article.is_featured" class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded">ফিচারড</span>
                                <span v-if="article.is_breaking" class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">ব্রেকিং</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span>{{ article.category?.name ?? '-' }}</span>
                            <span v-if="article.sub_category?.name" class="block text-xs text-gray-400">
                                {{ article.sub_category.name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-800">{{ article.author?.name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ truncateContent(article.content) }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span :class="statusClass(article.status)" class="text-xs px-2 py-1 rounded-full font-medium">
                                {{ statusLabel(article.status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ article.views }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ article.published_at ? formatDate(article.published_at) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    @click="openEditModal(article)"
                                    class="text-blue-600 hover:text-blue-800"
                                    title="সম্পাদনা"
                                >
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </button>
                                <button
                                    @click="openDeleteModal(article)"
                                    class="text-red-600 hover:text-red-800"
                                    title="মুছুন"
                                >
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="!articles.length">
                        <td colspan="8" class="text-center py-8 text-gray-400">
                            কোনো আর্টিকেল পাওয়া যায়নি।
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="flex justify-center items-center gap-2 mt-6">
            <button
                :disabled="meta.current_page === 1"
                @click="fetchArticles(meta.current_page - 1)"
                class="px-3 py-1 rounded-md border hover:bg-gray-50 disabled:opacity-40"
            >
                পূর্ববর্তী
            </button>
            <span class="text-sm text-gray-600">
                পৃষ্ঠা {{ meta.current_page }} / {{ meta.last_page }}
            </span>
            <button
                :disabled="meta.current_page === meta.last_page"
                @click="fetchArticles(meta.current_page + 1)"
                class="px-3 py-1 rounded-md border hover:bg-gray-50 disabled:opacity-40"
            >
                পরবর্তী
            </button>
        </div>

        <!-- Modals -->
        <ArticleFormModal
            :show="showFormModal"
            :article="selectedArticle"
            :categories="categories"
            @close="showFormModal = false"
            @saved="fetchArticles(meta.current_page)"
        />

        <ArticleDeleteModal
            :show="showDeleteModal"
            :article="selectedArticle"
            @close="showDeleteModal = false"
            @deleted="fetchArticles(meta.current_page)"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import api from '@/services/api';
import Swal from 'sweetalert2';
import ArticleFormModal from '@/components/admin/articles/ArticleFormModal.vue';
import ArticleDeleteModal from '@/components/admin/articles/ArticleDeleteModal.vue';

interface Category {
    id: number;
    name: string;
    parent_id: number | null;
    children?: Category[];
}

interface Author {
    id: number;
    name: string;
}

interface ArticleImage {
    id: number;
    url: string;
    caption: string | null;
    sort_order: number;
    is_cover: boolean;
}

interface Article {
    id: number;
    title: string;
    sub_title?: string | null;
    excerpt?: string | null;
    content: string;
    slug: string;
    featured_image: string | null;
    status: 'draft' | 'pending' | 'published' | 'archived';
    is_featured: boolean;
    is_breaking: boolean;
    views: number;
    published_at: string | null;
    category?: { id: number; name: string };
    category_id?: number;
    sub_category?: { id: number; name: string } | null;
    author?: { id: number; name: string };
    images?: ArticleImage[];  
}

const articles = ref<Article[]>([]);
const categories = ref<Category[]>([]); // top-level categories WITH children (from AdminCategoryResource)
const authors = ref<Author[]>([]);
const loading = ref(false);

const filters = reactive({
    status: '',
    search: '',
    category_id: '',
    sub_category_id: '',
    author_id: '',
    date_from: '',
    date_to: '',
});

const meta = reactive({ current_page: 1, last_page: 1, total: 0 });

const showFormModal = ref(false);
const showDeleteModal = ref(false);
const selectedArticle = ref<Article | null>(null);

// Sub-categories of the currently-selected parent category, for the dependent dropdown
const activeSubCategories = computed(() => {
    if (!filters.category_id) return [];
    const parent = categories.value.find((c) => c.id === Number(filters.category_id));
    return parent?.children ?? [];
});

const statusLabel = (status: string) => {
    const map: Record<string, string> = {
        draft: 'draft', pending: 'pending', published: 'published', archived: 'archived',
    };
    return map[status] ?? status;
};

const statusClass = (status: string) => {
    const map: Record<string, string> = {
        draft: 'bg-gray-100 text-gray-600',
        pending: 'bg-yellow-100 text-yellow-700',
        published: 'bg-green-100 text-green-700',
        archived: 'bg-red-100 text-red-700',
    };
    return map[status] ?? 'bg-gray-100 text-gray-600';
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('bn-BD', { year: 'numeric', month: 'short', day: 'numeric' });
};

const fetchCategories = async () => {
    try {
        // Your AdminCategoryController@index already returns parents with children eager-loaded
        const { data } = await api.get('/admin/categories');
        categories.value = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
    } catch (error) {
        console.error('Failed to load categories:', error);
    }
};

const fetchAuthors = async () => {
    try {
        const { data } = await api.get('/admin/authors');
        authors.value = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
    } catch (error) {
        console.error('Failed to load authors:', error);
    }
};

const fetchArticles = async (page = 1) => {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/articles', {
            params: {
                page,
                status: filters.status || undefined,
                search: filters.search || undefined,
                category_id: filters.category_id || undefined,
                sub_category_id: filters.sub_category_id || undefined,
                author_id: filters.author_id || undefined,
                date_from: filters.date_from || undefined,
                date_to: filters.date_to || undefined,
            },
        });

        articles.value = data.data ?? [];
        meta.current_page = data.meta?.current_page ?? 1;
        meta.last_page = data.meta?.last_page ?? 1;
        meta.total = data.meta?.total ?? 0;
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'ত্রুটি',
            text: error?.response?.data?.message || 'আর্টিকেল লোড করা যায়নি।',
            confirmButtonColor: '#4B5563',
        });
    } finally {
        loading.value = false;
    }
};

const truncateContent = (content: string, wordLimit = 10): string => {
    if (!content) return '-';

    // Strip HTML tags in case content is rich text/HTML from an editor
    const plainText = content.replace(/<[^>]*>/g, '').trim();

    const words = plainText.split(/\s+/);
    if (words.length <= wordLimit) return plainText;

    return words.slice(0, wordLimit).join(' ') + '...';
};

const onCategoryChange = () => {
    filters.sub_category_id = ''; // reset dependent filter when parent changes
    fetchArticles(1);
};

const resetFilters = () => {
    filters.status = '';
    filters.search = '';
    filters.category_id = '';
    filters.sub_category_id = '';
    filters.author_id = '';
    filters.date_from = '';
    filters.date_to = '';
    fetchArticles(1);
};

const openCreateModal = () => {
    selectedArticle.value = null;
    showFormModal.value = true;
};

const openEditModal = (article: Article) => {
    selectedArticle.value = {
        ...article,
        category_id: article.category?.id ?? article.category_id,
    };
    showFormModal.value = true;
};

const openDeleteModal = (article: Article) => {
    selectedArticle.value = article;
    showDeleteModal.value = true;
};

onMounted(() => {
    fetchArticles();
    fetchCategories();
    fetchAuthors();
});
</script>