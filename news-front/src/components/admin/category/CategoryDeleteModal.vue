<template>
    <Transition
        enter-active-class="transition duration-150"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="category" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-6 text-center">
                <div class="w-14 h-14 mx-auto rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-4">
                    <i class="bi bi-exclamation-triangle text-2xl"></i>
                </div>

                <h2 class="text-lg font-semibold text-gray-800 mb-1">আপনি কি নিশ্চিত?</h2>
                <p class="text-sm text-gray-500 mb-6">
                    "<span class="font-medium text-gray-700">{{ category.name }}</span>" ক্যাটাগরিটি স্থায়ীভাবে ডিলিট করা হবে। এই কাজটি পূর্বাবস্থায় ফেরানো যাবে না।
                </p>

                <div class="flex justify-center gap-3">
                    <button @click="$emit('close')" class="px-4 py-2 rounded-md border text-gray-600 hover:bg-gray-50">
                        বাতিল
                    </button>
                    <button
                        @click="handleDelete"
                        :disabled="deleting"
                        class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white disabled:opacity-50"
                    >
                        {{ deleting ? 'ডিলিট হচ্ছে...' : 'হ্যাঁ, ডিলিট করুন' }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'
import type { AdminCategory } from '@/types/category'

const props = defineProps<{
    category: AdminCategory | null
}>()

const emit = defineEmits<{
    close: []
    deleted: []
}>()

const deleting = ref(false)

const handleDelete = async () => {
    if (!props.category) return

    deleting.value = true
    try {
        await api.delete(`/admin/categories/${props.category.id}`)
        emit('deleted')
    } catch (e: any) {
        Swal.fire('ত্রুটি', e?.response?.data?.message || 'ডিলিট ব্যর্থ হয়েছে।', 'error')
    } finally {
        deleting.value = false
    }
}
</script>