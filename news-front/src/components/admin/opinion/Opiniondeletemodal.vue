<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="emit('close')">
        <div class="bg-white rounded-lg w-full max-w-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2">মতামত মুছবেন?</h2>
            <p class="text-sm text-gray-600 mb-5">
                আপনি কি নিশ্চিত যে
                <span class="font-semibold text-gray-900">"{{ opinion?.title }}"</span>
                মুছে ফেলতে চান? এই কাজটি ফিরিয়ে আনা যাবে না।
            </p>

            <p v-if="submitError" class="text-sm text-red-600 mb-3">{{ submitError }}</p>

            <div class="flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="emit('close')"
                    class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100"
                >
                    বাতিল
                </button>
                <button
                    type="button"
                    @click="confirmDelete"
                    :disabled="deleting"
                    class="px-4 py-2 rounded-md text-sm font-semibold text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
                >
                    {{ deleting ? 'মুছে ফেলা হচ্ছে...' : 'মুছে ফেলুন' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import api from '@/services/api'
import type { Opinion } from '@/types/opinion';

const props = defineProps<{
    opinion: Opinion | null
}>()

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'deleted'): void
}>()

const deleting = ref(false)
const submitError = ref('')

const confirmDelete = async () => {
    if (!props.opinion) return
    deleting.value = true
    submitError.value = ''

    try {
        await api.delete(`/admin/opinions/${props.opinion.id}`)
        emit('deleted')
    } catch (error) {
        submitError.value = 'মুছে ফেলা যায়নি, আবার চেষ্টা করুন।'
        console.error('Failed to delete opinion:', error)
    } finally {
        deleting.value = false
    }
}
</script>