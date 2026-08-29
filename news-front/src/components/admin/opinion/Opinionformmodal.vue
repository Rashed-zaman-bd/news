<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="emit('close')">
        <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                <h2 class="text-lg font-bold text-gray-900">
                    {{ isEdit ? 'মতামত সম্পাদনা' : 'নতুন মতামত' }}
                </h2>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">
                    &times;
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শিরোনাম *</label>
                    <input
                        v-model="form.title"
                        type="text"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                    <p v-if="errors.title" class="text-xs text-red-600 mt-1">{{ errors.title[0] }}</p>
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        স্লাগ <span class="text-gray-400 font-normal">(খালি রাখলে স্বয়ংক্রিয়ভাবে তৈরি হবে)</span>
                    </label>
                    <input
                        v-model="form.slug"
                        type="text"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                    <p v-if="errors.slug" class="text-xs text-red-600 mt-1">{{ errors.slug[0] }}</p>
                </div>

                <!-- Writer name / designation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">লেখকের নাম *</label>
                        <input
                            v-model="form.writer_name"
                            type="text"
                            required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                        />
                        <p v-if="errors.writer_name" class="text-xs text-red-600 mt-1">{{ errors.writer_name[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">পদবি</label>
                        <input
                            v-model="form.writer_designation"
                            type="text"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                        />
                    </div>
                </div>

                <!-- Writer image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">লেখকের ছবি</label>
                    <div class="flex items-center gap-3">
                        <img
                            v-if="writerImagePreview"
                            :src="writerImagePreview"
                            alt=""
                            class="w-14 h-14 rounded-full object-cover border border-gray-200"
                        />
                        <input type="file" accept="image/*" @change="onWriterImageChange" class="text-sm" />
                    </div>
                    <p v-if="errors.writer_image" class="text-xs text-red-600 mt-1">{{ errors.writer_image[0] }}</p>
                </div>

                <!-- Featured image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ফিচার ছবি</label>
                    <div class="flex items-center gap-3">
                        <img
                            v-if="imagePreview"
                            :src="imagePreview"
                            alt=""
                            class="w-24 h-16 rounded object-cover border border-gray-200"
                        />
                        <input type="file" accept="image/*" @change="onImageChange" class="text-sm" />
                    </div>
                    <p v-if="errors.image" class="text-xs text-red-600 mt-1">{{ errors.image[0] }}</p>
                </div>

                <!-- Text -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">মতামতের বিষয়বস্তু *</label>
                    <textarea
                        v-model="form.text"
                        rows="6"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    ></textarea>
                    <p v-if="errors.text" class="text-xs text-red-600 mt-1">{{ errors.text[0] }}</p>
                </div>

                <!-- Sort order + published -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ক্রম নম্বর</label>
                        <input
                            v-model.number="form.sort_order"
                            type="number"
                            min="0"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                        />
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input v-model="form.is_published" type="checkbox" class="w-4 h-4 rounded" />
                            <span class="text-sm font-medium text-gray-700">প্রকাশ করুন</span>
                        </label>
                    </div>
                </div>

                <p v-if="submitError" class="text-sm text-red-600">{{ submitError }}</p>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button
                        type="button"
                        @click="emit('close')"
                        class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100"
                    >
                        বাতিল
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="px-4 py-2 rounded-md text-sm font-semibold text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
                    >
                        {{ submitting ? 'সংরক্ষণ হচ্ছে...' : 'সংরক্ষণ করুন' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import api from '@/services/api'
import type { Opinion } from '@/components/admin/opinion/Opinionformmodal.vue'

const props = defineProps<{
    opinion: Opinion | null
}>()

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'saved'): void
}>()

const isEdit = computed(() => !!props.opinion)

const form = reactive({
    title: '',
    slug: '',
    writer_name: '',
    writer_designation: '',
    text: '',
    sort_order: 0,
    is_published: false,
})

const writerImageFile = ref<File | null>(null)
const imageFile = ref<File | null>(null)
const writerImagePreview = ref<string | null>(null)
const imagePreview = ref<string | null>(null)

const submitting = ref(false)
const submitError = ref('')
const errors = ref<Record<string, string[]>>({})

const onWriterImageChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (!file) return
    writerImageFile.value = file
    writerImagePreview.value = URL.createObjectURL(file)
}

const onImageChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (!file) return
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
}

const submit = async () => {
    submitting.value = true
    submitError.value = ''
    errors.value = {}

    const payload = new FormData()
    payload.append('title', form.title)
    if (form.slug) payload.append('slug', form.slug)
    payload.append('writer_name', form.writer_name)
    if (form.writer_designation) payload.append('writer_designation', form.writer_designation)
    payload.append('text', form.text)
    payload.append('sort_order', String(form.sort_order))
    payload.append('is_published', form.is_published ? '1' : '0')
    if (writerImageFile.value) payload.append('writer_image', writerImageFile.value)
    if (imageFile.value) payload.append('image', imageFile.value)

    try {
        if (isEdit.value && props.opinion) {
            // Laravel doesn't parse multipart PUT/PATCH bodies natively,
            // so spoof the method and POST instead.
            payload.append('_method', 'PUT')
            await api.post(`/admin/opinions/${props.opinion.id}`, payload, {
                headers: { 'Content-Type': 'multipart/form-data' },
            })
        } else {
            await api.post('/admin/opinions', payload, {
                headers: { 'Content-Type': 'multipart/form-data' },
            })
        }
        emit('saved')
    } catch (error: any) {
        if (error?.response?.status === 422) {
            errors.value = error.response.data.errors ?? {}
            submitError.value = error.response.data.message ?? 'ফর্মে ত্রুটি রয়েছে, অনুগ্রহ করে পরীক্ষা করুন।'
        } else {
            submitError.value = 'একটি সমস্যা হয়েছে, আবার চেষ্টা করুন।'
            console.error('Failed to save opinion:', error)
        }
    } finally {
        submitting.value = false
    }
}

onMounted(() => {
    if (props.opinion) {
        form.title = props.opinion.title
        form.slug = props.opinion.slug
        form.writer_name = props.opinion.writer_name
        form.writer_designation = props.opinion.writer_designation ?? ''
        form.text = props.opinion.text
        form.sort_order = props.opinion.sort_order
        form.is_published = props.opinion.is_published

        writerImagePreview.value = props.opinion.writer_image
        imagePreview.value = props.opinion.image
    }
})
</script>