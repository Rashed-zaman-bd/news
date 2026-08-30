<template>
    <div class="p-4 md:p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">মতামত ব্যবস্থাপনা</h1>
            <button
                @click="openCreateModal"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-md transition-colors"
            >
                + নতুন মতামত
            </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <input
                v-model="search"
                @input="debouncedSearch"
                type="text"
                placeholder="শিরোনাম দিয়ে খুঁজুন..."
                class="border border-gray-300 rounded-md px-3 py-2 text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-red-500"
            />

            <select
                v-model="statusFilter"
                @change="fetchOpinions(1)"
                class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
            >
                <option value="">সব স্ট্যাটাস</option>
                <option value="1">প্রকাশিত</option>
                <option value="0">অপ্রকাশিত</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white border border-gray-200 rounded-md overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-left text-gray-600">
                        <th class="px-4 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold">ছবি</th>
                        <th class="px-4 py-3 font-semibold">শিরোনাম</th>
                        <th class="px-4 py-3 font-semibold">লেখক</th>
                        <th class="px-4 py-3 font-semibold">স্ট্যাটাস</th>
                        <th class="px-4 py-3 font-semibold">প্রকাশের তারিখ</th>
                        <th class="px-4 py-3 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="loading">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">লোড হচ্ছে...</td>
                    </tr>
                    <tr v-else-if="opinions.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">কোনো মতামত পাওয়া যায়নি</td>
                    </tr>
                    <tr
                        v-for="(opinion, i) in opinions"
                        :key="opinion.id"
                        class="hover:bg-gray-50 transition-colors"
                    >
                        <td class="px-4 py-3 text-gray-500">{{ (meta.current_page - 1) * meta.per_page + i + 1 }}</td>
                        <td class="px-4 py-3">
                            <img
                                v-if="opinion.image"
                                :src="opinion.image"
                                alt=""
                                class="w-12 h-12 object-cover rounded"
                            />
                            <div v-else class="w-12 h-12 rounded bg-gray-100"></div>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="font-medium text-gray-900 line-clamp-2">{{ opinion.title }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <img
                                    v-if="opinion.writer_image"
                                    :src="opinion.writer_image"
                                    alt=""
                                    class="w-7 h-7 rounded-full object-cover"
                                />
                                <div>
                                    <p class="text-gray-800">{{ opinion.writer_name }}</p>
                                    <p v-if="opinion.writer_designation" class="text-xs text-gray-400">
                                        {{ opinion.writer_designation }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-block px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="opinion.is_published
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-500'"
                            >
                                {{ opinion.is_published ? 'প্রকাশিত' : 'অপ্রকাশিত' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                            {{ formatDate(opinion.published_at) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    @click="openEditModal(opinion)"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-semibold cursor-pointer"
                                >
                                    edit
                                </button>
                                <button
                                    @click="openDeleteModal(opinion)"
                                    class="text-red-600 hover:text-red-800 text-xs font-semibold cursor-pointer"
                                >
                                    delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-2 mt-4">
            <button
                :disabled="meta.current_page <= 1"
                @click="fetchOpinions(meta.current_page - 1)"
                class="px-3 py-1.5 rounded border border-gray-300 text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50"
            >
                পূর্ববর্তী
            </button>
            <span class="text-sm text-gray-600">
                {{ meta.current_page }} / {{ meta.last_page }}
            </span>
            <button
                :disabled="meta.current_page >= meta.last_page"
                @click="fetchOpinions(meta.current_page + 1)"
                class="px-3 py-1.5 rounded border border-gray-300 text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50"
            >
                পরবর্তী
            </button>
        </div>

        <!-- Create/Edit Modal -->
        <OpinionFormModal
            v-if="showFormModal"
            :opinion="selectedOpinion"
            @close="showFormModal = false"
            @saved="handleSaved"
        />

        <!-- Delete Confirm Modal -->
        <OpinionDeleteModal
            v-if="showDeleteModal"
            :opinion="selectedOpinion"
            @close="showDeleteModal = false"
            @deleted="handleDeleted"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'
import OpinionFormModal from '@/components/admin/opinion/Opinionformmodal.vue'
import OpinionDeleteModal from '@/components/admin/opinion/Opiniondeletemodal.vue'

export interface Opinion {
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
    created_at: string
    updated_at: string
}

const opinions = ref<Opinion[]>([])
const loading = ref(false)
const search = ref('')
const statusFilter = ref('')

const meta = reactive({
    current_page: 1,
    last_page: 1,
    per_page: 15,
})

const showFormModal = ref(false)
const showDeleteModal = ref(false)
const selectedOpinion = ref<Opinion | null>(null)

let searchTimeout: ReturnType<typeof setTimeout> | null = null
const debouncedSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => fetchOpinions(1), 400)
}

const fetchOpinions = async (page = 1) => {
    loading.value = true
    try {
        const { data } = await api.get('/admin/opinions', {
            params: {
                page,
                per_page: meta.per_page,
                search: search.value || undefined,
                is_published: statusFilter.value !== '' ? statusFilter.value : undefined,
            },
        })

        opinions.value = data.data ?? []
        meta.current_page = data.meta?.current_page ?? 1
        meta.last_page = data.meta?.last_page ?? 1
        meta.per_page = data.meta?.per_page ?? meta.per_page
    } catch (error) {
        console.error('Failed to load opinions:', error)
        opinions.value = []
    } finally {
        loading.value = false
    }
}

const formatDate = (dateStr: string | null): string => {
    if (!dateStr) return '—'
    return new Date(dateStr).toLocaleDateString('bn-BD', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const openCreateModal = () => {
    selectedOpinion.value = null
    showFormModal.value = true
}

const openEditModal = (opinion: Opinion) => {
    selectedOpinion.value = opinion
    showFormModal.value = true
}

const openDeleteModal = (opinion: Opinion) => {
    selectedOpinion.value = opinion
    showDeleteModal.value = true
}

const handleSaved = () => {
    showFormModal.value = false
    fetchOpinions(meta.current_page)
}

const handleDeleted = () => {
    showDeleteModal.value = false
    fetchOpinions(meta.current_page)
}

onMounted(() => {
    fetchOpinions()
})
</script>