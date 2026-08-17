<template>
    <Transition
        enter-active-class="transition duration-150"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">
                        {{ editingCategory ? 'ক্যাটাগরি সম্পাদনা' : (form.parent_id ? 'সাব-ক্যাটাগরি তৈরি' : 'নতুন ক্যাটাগরি তৈরি') }}
                    </h2>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">নাম *</label>
                        <input v-model="form.name" type="text" required
                            class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300">
                        <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">স্লাগ (খালি রাখলে স্বয়ংক্রিয়ভাবে তৈরি হবে)</label>
                        <input v-model="form.slug" type="text"
                            class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300">
                        <p v-if="errors.slug" class="text-red-500 text-xs mt-1">{{ errors.slug[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">আইকন (Bootstrap Icons নাম, যেমন: trophy)</label>
                        <input v-model="form.icon" type="text" placeholder="trophy"
                            class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">প্যারেন্ট ক্যাটাগরি</label>
                        <select v-model="form.parent_id"
                            class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300">
                            <option :value="null">— কোনোটি নয় (মূল ক্যাটাগরি) —</option>
                            <option v-for="p in parentOptions" :key="p.id" :value="p.id">
                                {{ p.name }}
                            </option>
                        </select>
                        <p v-if="errors.parent_id" class="text-red-500 text-xs mt-1">{{ errors.parent_id[0] }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">অর্ডার</label>
                            <input v-model.number="form.order" type="number" min="0"
                                class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300">
                        </div>
                        <div class="flex items-center gap-2 mt-6">
                            <input v-model="form.is_active" type="checkbox" id="is_active" class="w-4 h-4">
                            <label for="is_active" class="text-sm">সক্রিয়</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="$emit('close')" class="px-4 py-2 rounded-md border text-gray-600 hover:bg-gray-50">
                            বাতিল
                        </button>
                        <button type="submit" :disabled="submitting"
                            class="px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white disabled:opacity-50">
                            {{ submitting ? 'সংরক্ষণ হচ্ছে...' : 'সংরক্ষণ করুন' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'
import type { AdminCategory } from '@/types/category'

const props = defineProps<{
    open: boolean
    editingCategory: AdminCategory | null
    initialParentId: number | null
    parentOptions: AdminCategory[]
}>()

const emit = defineEmits<{
    close: []
    saved: []
}>()

const emptyForm = () => ({
    name: '',
    slug: '',
    icon: '',
    parent_id: null as number | null,
    order: 0,
    is_active: true,
})

const form = reactive(emptyForm())
const errors = reactive<Record<string, string[]>>({})
const submitting = ref(false)   // fixed: was reactive({ value: false })

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) return

        Object.keys(errors).forEach((k) => delete errors[k])

        if (props.editingCategory) {
            Object.assign(form, {
                name: props.editingCategory.name,
                slug: props.editingCategory.slug,
                icon: props.editingCategory.icon || '',
                parent_id: props.editingCategory.parent_id,
                order: props.editingCategory.order,
                is_active: props.editingCategory.is_active,
            })
        } else {
            Object.assign(form, emptyForm())
            form.parent_id = props.initialParentId
        }
    }
)

const handleSubmit = async () => {
    submitting.value = true
    Object.keys(errors).forEach((k) => delete errors[k])

    const payload = {
        name: form.name,
        slug: form.slug || undefined,
        icon: form.icon || undefined,
        parent_id: form.parent_id,
        order: form.order,
        is_active: form.is_active,
    }

    try {
        if (props.editingCategory) {
            await api.put(`/admin/categories/${props.editingCategory.id}`, payload)
        } else {
            await api.post('/admin/categories', payload)
        }

        await Swal.fire({
            icon: 'success',
            title: props.editingCategory ? 'আপডেট সফল হয়েছে' : 'ক্যাটাগরি তৈরি হয়েছে',
            timer: 1500,
            showConfirmButton: false,
        })

        emit('saved')
    } catch (e: any) {
        if (e?.response?.status === 422) {
            Object.assign(errors, e.response.data.errors || {})
        } else {
            Swal.fire('ত্রুটি', e?.response?.data?.message || 'সংরক্ষণ ব্যর্থ হয়েছে।', 'error')
        }
    } finally {
        submitting.value = false
    }
}
</script>