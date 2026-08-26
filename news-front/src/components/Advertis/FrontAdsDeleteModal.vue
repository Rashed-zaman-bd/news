<template>
    <Teleport to="body">
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl w-full max-w-sm p-5">

                <h2 class="text-lg font-bold text-gray-900 mb-2">বিজ্ঞাপন মুছে ফেলুন</h2>

                <p class="text-sm text-gray-600 mb-4">
                    আপনি কি নিশ্চিত যে আপনি
                    <span class="font-medium text-gray-900">"{{ advertisement?.name }}"</span>
                    মুছে ফেলতে চান? এই কাজটি অপরিবর্তনীয়।
                </p>

                <div class="flex justify-end gap-2">
                    <button
                        @click="$emit('close')"
                        class="px-4 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50"
                    >
                        বাতিল
                    </button>
                    <button
                        @click="confirmDelete"
                        :disabled="deleting"
                        class="px-4 py-2 rounded-lg text-sm bg-red-600 text-white hover:bg-red-700 disabled:opacity-50"
                    >
                        {{ deleting ? 'মুছে ফেলা হচ্ছে...' : 'মুছে ফেলুন' }}
                    </button>
                </div>

            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import api from '@/services/api'

interface Advertisement {
    id: number
    name: string
}

const props = defineProps<{
    advertisement: Advertisement | null
}>()

const emit = defineEmits<{
    close: []
    deleted: []
}>()

const deleting = ref(false)

const confirmDelete = async () => {
    if (!props.advertisement) return

    deleting.value = true
    try {
        await api.delete(`/front-ads/${props.advertisement.id}`)
        emit('deleted')
    } catch (error) {
        console.error('Failed to delete advertisement:', error)
    } finally {
        deleting.value = false
    }
}
</script>