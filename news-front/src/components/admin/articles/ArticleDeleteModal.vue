<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <i class="bi bi-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">আর্টিকেল মুছে ফেলবেন?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        "<span class="font-medium">{{ article?.title }}</span>" আর্টিকেলটি স্থায়ীভাবে মুছে ফেলা হবে। এই কাজটি ফিরিয়ে নেওয়া যাবে না।
                    </p>

                    <div class="flex gap-3 w-full">
                        <button
                            @click="$emit('close')"
                            class="flex-1 px-4 py-2 rounded-md border text-sm text-gray-600 hover:bg-gray-50"
                        >
                            বাতিল
                        </button>
                        <button
                            @click="handleDelete"
                            :disabled="deleting"
                            class="flex-1 px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm disabled:opacity-50 flex items-center justify-center gap-2"
                        >
                            <i v-if="deleting" class="bi bi-arrow-repeat animate-spin"></i>
                            মুছে ফেলুন
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import api from '@/services/api';
import Swal from 'sweetalert2';

interface ArticleLite {
    id: number;
    title: string;
}

const props = defineProps<{
    show: boolean;
    article: ArticleLite | null;
}>();

const emit = defineEmits<{
    close: [];
    deleted: [];
}>();

const deleting = ref(false);

const handleDelete = async () => {
    if (!props.article) return;

    deleting.value = true;
    try {
        await api.delete(`/admin/articles/${props.article.id}`);

        Swal.fire({
            icon: 'success',
            title: 'মুছে ফেলা হয়েছে',
            timer: 1500,
            showConfirmButton: false,
        });

        emit('deleted');
        emit('close');
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'ত্রুটি',
            text: error.response?.data?.message || 'মুছে ফেলা যায়নি।',
            confirmButtonColor: '#4B5563',
        });
    } finally {
        deleting.value = false;
    }
};
</script>