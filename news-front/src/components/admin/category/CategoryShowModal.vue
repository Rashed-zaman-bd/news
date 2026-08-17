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
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">ক্যাটাগরি বিস্তারিত</h2>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <i :class="`bi bi-${category.icon || 'folder2'} text-xl`"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ category.name }}</p>
                            <p class="text-xs text-gray-500">/{{ category.slug }}</p>
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-y-3 text-sm border-t pt-4">
                        <dt class="text-gray-500">স্ট্যাটাস</dt>
                        <dd>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                  :class="category.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'">
                                {{ category.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>

                        <dt class="text-gray-500">প্যারেন্ট</dt>
                        <dd class="text-gray-800">{{ category.parent_name || '— মূল ক্যাটাগরি —' }}</dd>

                        <dt class="text-gray-500">অর্ডার</dt>
                        <dd class="text-gray-800">{{ category.order }}</dd>

                        <dt class="text-gray-500">সাব-ক্যাটাগরি</dt>
                        <dd class="text-gray-800">{{ category.children?.length || 0 }} টি</dd>

                        <dt class="text-gray-500">তৈরি হয়েছে</dt>
                        <dd class="text-gray-800">{{ formatDate(category.created_at) }}</dd>
                    </dl>

                    <div v-if="category.children?.length" class="border-t pt-4">
                        <p class="text-sm text-gray-500 mb-2">সাব-ক্যাটাগরিসমূহ:</p>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="child in category.children"
                                :key="child.id"
                                class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-700"
                            >
                                {{ child.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <button @click="$emit('close')" class="px-4 py-2 rounded-md border text-gray-600 hover:bg-gray-50">
                        বন্ধ করুন
                    </button>
                    <button @click="$emit('edit', category)" class="px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white">
                        সম্পাদনা করুন
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
import type { AdminCategory } from '@/types/category'

defineProps<{
    category: AdminCategory | null
}>()

defineEmits<{
    close: []
    edit: [category: AdminCategory]
}>()

const formatDate = (iso?: string) =>
    iso ? new Date(iso).toLocaleDateString('bn-BD', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
</script>