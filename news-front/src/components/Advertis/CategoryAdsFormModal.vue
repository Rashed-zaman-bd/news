<template>
    <Teleport to="body">
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">
                        {{ isEdit ? 'বিজ্ঞাপন সম্পাদনা' : 'নতুন বিজ্ঞাপন' }}
                    </h2>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="submit" class="px-5 py-4 space-y-4">

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ছবি</label>

                        <div
                            v-if="imagePreview"
                            class="mb-2 border border-gray-200 rounded-lg overflow-hidden"
                        >
                            <img :src="imagePreview" class="w-full h-32 object-cover" />
                        </div>

                        <input
                            type="file"
                            accept="image/png, image/jpeg, image/webp, image/gif"
                            @change="handleFileChange"
                            class="block w-full text-sm text-gray-600
                                   file:mr-3 file:py-2 file:px-3
                                   file:rounded-lg file:border-0
                                   file:bg-gray-100 file:text-gray-700
                                   hover:file:bg-gray-200"
                        />
                        <p v-if="!isEdit" class="text-xs text-gray-400 mt-1">JPG, PNG বা WebP, সর্বোচ্চ ২MB</p>
                        <p v-if="errors.image" class="text-xs text-red-600 mt-1">{{ errors.image[0] }}</p>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">বিজ্ঞাপনের নাম</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            placeholder="যেমন: IPDC Deposit Banner"
                        />
                        <p v-if="errors.name" class="text-xs text-red-600 mt-1">{{ errors.name[0] }}</p>
                    </div>

                    <!-- Provider -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">বিজ্ঞাপনদাতা</label>
                        <input
                            v-model="form.provider"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            placeholder="যেমন: IPDC Finance"
                        />
                        <p v-if="errors.provider" class="text-xs text-red-600 mt-1">{{ errors.provider[0] }}</p>
                    </div>

                    <!-- Link URL -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">লিংক (ঐচ্ছিক)</label>
                        <input
                            v-model="form.link_url"
                            type="url"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            placeholder="https://example.com"
                        />
                        <p v-if="errors.link_url" class="text-xs text-red-600 mt-1">{{ errors.link_url[0] }}</p>
                    </div>

                    <!-- Placement + Sort order -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">অবস্থান</label>
                            <select
                                v-model="form.placement"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                                <option value="top">Top</option>
                                <option value="middle">Middle</option>
                                <option value="middle-two">middle-two</option>
                                <option value="middle-three">middle-three</option>
                                <option value="sidebar">Sidebar</option>
                                <option value="sidebar-two">sidebar-two</option>
                            </select>
                            <p v-if="errors.placement" class="text-xs text-red-600 mt-1">{{ errors.placement[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">অর্ডার</label>
                            <input
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            />
                        </div>
                    </div>

                    <!-- Start/End dates -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শুরুর তারিখ (ঐচ্ছিক)</label>
                            <input
                                v-model="form.starts_at"
                                type="datetime-local"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">শেষের তারিখ (ঐচ্ছিক)</label>
                            <input
                                v-model="form.ends_at"
                                type="datetime-local"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            />
                            <p v-if="errors.ends_at" class="text-xs text-red-600 mt-1">{{ errors.ends_at[0] }}</p>
                        </div>
                    </div>

                    <!-- Active toggle -->
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300" />
                        সক্রিয় রাখুন
                    </label>

                    <!-- Actions -->
                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-4 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50"
                        >
                            বাতিল
                        </button>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="px-4 py-2 rounded-lg text-sm bg-red-600 text-white hover:bg-red-700 disabled:opacity-50"
                        >
                            {{ submitting ? 'সংরক্ষণ হচ্ছে...' : 'সংরক্ষণ করুন' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

interface Advertisement {
    id: number
    image: string
    name: string
    provider: string
    link_url: string | null
    placement: 'top' | 'middle' | 'sidebar' | 'middle-two' | 'middle-three' | 'sidebar-two'
    sort_order: number
    is_active: boolean
    starts_at?: string | null
    ends_at?: string | null
}

const props = defineProps<{
    advertisement: Advertisement | null
}>()

const emit = defineEmits<{
    close: []
    saved: []
}>()

const isEdit = computed(() => !!props.advertisement)

const form = ref({
    name: '',
    provider: '',
    link_url: '',
    placement: 'middle' as 'top' | 'middle' | 'sidebar' | 'middle-two' | 'middle-three' | 'sidebar-two',
    sort_order: 0,
    is_active: true,
    starts_at: '',
    ends_at: '',
})

const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const errors = ref<Record<string, string[]>>({})
const submitting = ref(false)

const handleFileChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (!file) return

    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
}

const submit = async () => {
    errors.value = {}
    submitting.value = true

    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('provider', form.value.provider)
    formData.append('placement', form.value.placement)
    formData.append('sort_order', String(form.value.sort_order))
    formData.append('is_active', form.value.is_active ? '1' : '0')

    if (form.value.link_url) formData.append('link_url', form.value.link_url)
    if (form.value.starts_at) formData.append('starts_at', form.value.starts_at)
    if (form.value.ends_at) formData.append('ends_at', form.value.ends_at)
    if (imageFile.value) formData.append('image', imageFile.value)

    try {
        if (isEdit.value && props.advertisement) {
            // _method spoofing for multipart PUT — same pattern as ArticleFormModal
            formData.append('_method', 'PUT')
            await api.post(`/category-ads/${props.advertisement.id}`, formData)
        } else {
            await api.post('/category-ads', formData)
        }

        emit('saved')
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {}
        } else {
            console.error('Failed to save advertisement:', error)
        }
    } finally {
        submitting.value = false
    }
}

onMounted(() => {
    if (props.advertisement) {
        form.value = {
            name: props.advertisement.name ?? '',
            provider: props.advertisement.provider ?? '',
            link_url: props.advertisement.link_url ?? '',
            placement: props.advertisement.placement ?? 'middle',
            sort_order: props.advertisement.sort_order ?? 0,
            is_active: props.advertisement.is_active ?? true,
            starts_at: props.advertisement.starts_at?.slice(0, 16) ?? '',
            ends_at: props.advertisement.ends_at?.slice(0, 16) ?? '',
        }
        imagePreview.value = props.advertisement.image
    }
})
</script>