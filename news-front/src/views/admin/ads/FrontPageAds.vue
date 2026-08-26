<template>
    <div class="max-w-7xl mx-auto px-4 py-6">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">বিজ্ঞাপন ব্যবস্থাপনা</h1>

            <button
                @click="openCreateModal"
                class="bg-red-600 hover:bg-red-700 text-white
                       px-4 py-2 rounded-lg text-sm font-medium
                       flex items-center gap-2"
            >
                <i class="bi bi-plus-lg"></i>
                নতুন বিজ্ঞাপন
            </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-5">
            <select
                v-model="placementFilter"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm"
            >
                <option value="">সব অবস্থান</option>
                <option value="top">Top</option>
                <option value="middle">Middle</option>
                <option value="sidebar">Sidebar</option>
            </select>

            <select
                v-model="statusFilter"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm"
            >
                <option value="">সব স্ট্যাটাস</option>
                <option value="1">সক্রিয়</option>
                <option value="0">নিষ্ক্রিয়</option>
            </select>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-16 text-gray-400">
            <i class="bi bi-arrow-repeat animate-spin text-3xl"></i>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3">ছবি</th>
                        <th class="px-4 py-3">নাম</th>
                        <th class="px-4 py-3">প্রোভাইডার</th>
                        <th class="px-4 py-3">অবস্থান</th>
                        <th class="px-4 py-3">অর্ডার</th>
                        <th class="px-4 py-3">ক্লিক</th>
                        <th class="px-4 py-3">স্ট্যাটাস</th>
                        <th class="px-4 py-3 text-right">অ্যাকশন</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <tr v-for="ad in ads" :key="ad.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <img :src="ad.image" :alt="ad.name" class="w-20 h-12 object-cover rounded border border-gray-200" />
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ ad.name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ ad.provider }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700 capitalize">
                                {{ ad.placement }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ ad.sort_order }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ ad.clicks ?? 0 }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="ad.is_active
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-500'"
                            >
                                {{ ad.is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    @click="openEditModal(ad)"
                                    class="text-gray-500 hover:text-red-600 p-1.5"
                                    title="সম্পাদনা"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    @click="openDeleteModal(ad)"
                                    class="text-gray-500 hover:text-red-600 p-1.5"
                                    title="মুছে ফেলুন"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="ads.length === 0">
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                            কোনো বিজ্ঞাপন পাওয়া যায়নি
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modals -->
        <FrontAdsFormModa
            v-if="showFormModal"
            :advertisement="selectedAd"
            @close="showFormModal = false"
            @saved="handleSaved"
        />

        <FrontAdsDeleteModal
            v-if="showDeleteModal"
            :advertisement="selectedAd"
            @close="showDeleteModal = false"
            @deleted="handleDeleted"
        />

    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import api from '@/services/api'
import FrontAdsFormModa from '@/components/Advertis/FrontAdsFormModal.vue'
import FrontAdsDeleteModal from '@/components/Advertis/FrontAdsDeleteModal.vue'

interface Advertisement {
    id: number
    image: string
    name: string
    provider: string
    link_url: string | null
    placement: 'top' | 'middle' | 'sidebar'
    sort_order: number
    is_active: boolean
    clicks?: number
    starts_at?: string | null
    ends_at?: string | null
}

const ads = ref<Advertisement[]>([])
const loading = ref(false)

const placementFilter = ref('')
const statusFilter = ref('')

const showFormModal = ref(false)
const showDeleteModal = ref(false)
const selectedAd = ref<Advertisement | null>(null)

const fetchAds = async () => {
    loading.value = true
    try {
        const { data } = await api.get('/admin/front-ads', {
            params: {
                placement: placementFilter.value || undefined,
                is_active: statusFilter.value !== '' ? statusFilter.value : undefined,
                limit: 100,
            },
        })
        ads.value = data.data ?? []
    } catch (error) {
        console.error('Failed to load category page ads:', error)
    } finally {
        loading.value = false
    }
}

const openCreateModal = () => {
    selectedAd.value = null
    showFormModal.value = true
}

const openEditModal = (ad: Advertisement) => {
    selectedAd.value = ad
    showFormModal.value = true
}

const openDeleteModal = (ad: Advertisement) => {
    selectedAd.value = ad
    showDeleteModal.value = true
}

const handleSaved = () => {
    showFormModal.value = false
    fetchAds()
}

const handleDeleted = () => {
    showDeleteModal.value = false
    fetchAds()
}

watch([placementFilter, statusFilter], fetchAds)

onMounted(fetchAds)
</script>