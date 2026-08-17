<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">ক্যাটাগরি ম্যানেজমেন্ট</h1>
            <button
                @click="openCreate(null)"
                class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition"
            >
                <i class="bi bi-plus-lg"></i>
                নতুন ক্যাটাগরি
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input
                    v-model="filters.search"
                    type="text"
                    placeholder="নাম বা স্লাগ দিয়ে খুঁজুন..."
                    class="w-full border rounded-md pl-9 pr-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
                >
                <button
                    v-if="filters.search"
                    @click="filters.search = ''"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>

            <select
                v-model="filters.status"
                class="border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
            >
                <option value="">সব স্ট্যাটাস</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <select
                v-model="filters.type"
                class="border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
            >
                <option value="">সব ধরন</option>
                <option value="parent">শুধু মূল ক্যাটাগরি</option>
                <option value="child">শুধু সাব-ক্যাটাগরি</option>
            </select>

            <button
                v-if="hasActiveFilters"
                @click="resetFilters"
                class="px-3 py-2 rounded-md border text-gray-500 hover:bg-gray-50 whitespace-nowrap text-sm"
            >
                <i class="bi bi-arrow-counterclockwise mr-1"></i>
                রিসেট
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div v-if="loading" class="p-10 text-center text-gray-500">
                <i class="bi bi-arrow-repeat animate-spin text-2xl"></i>
                <p class="mt-2">লোড হচ্ছে...</p>
            </div>

            <div v-else-if="filteredCategories.length === 0" class="p-10 text-center text-gray-500">
                <i class="bi bi-inbox text-3xl mb-2 block"></i>
                {{ hasActiveFilters ? 'ফিল্টার অনুযায়ী কোনো ক্যাটাগরি পাওয়া যায়নি।' : 'কোনো ক্যাটাগরি নেই।' }}
            </div>

            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left border-b">
                    <tr>
                        <th class="px-4 py-3">নাম</th>
                        <th class="px-4 py-3">স্লাগ</th>
                        <th class="px-4 py-3">স্ট্যাটাস</th>
                        <th class="px-4 py-3">অর্ডার</th>
                        <th class="px-4 py-3 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <template v-for="cat in filteredCategories" :key="cat.id">
                        <tr v-if="filters.type !== 'child'" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                <button v-if="cat.children.length" @click="toggleExpand(cat.id)" class="mr-2 text-gray-400">
                                    <i class="bi" :class="expanded.has(cat.id) ? 'bi-chevron-down' : 'bi-chevron-right'"></i>
                                </button>
                                <i v-if="cat.icon" :class="`bi bi-${cat.icon} mr-1`"></i>
                                <button class="hover:underline" @click="viewingCategory = cat">
                                    <span v-html="highlight(cat.name)"></span>
                                </button>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ cat.slug }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                      :class="cat.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'">
                                    {{ cat.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ cat.order }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openCreate(cat.id)" class="p-2 rounded hover:bg-gray-100 text-gray-600" title="সাব-ক্যাটাগরি যোগ করুন">
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                    <button @click="openEdit(cat)" class="p-2 rounded hover:bg-gray-100 text-gray-600" title="সম্পাদনা">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="deletingCategory = cat" class="p-2 rounded hover:bg-red-50 text-red-600" title="ডিলিট">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr
                            v-for="child in (filters.type === 'child' || expanded.has(cat.id) ? cat.children : [])"
                            :key="child.id"
                            class="bg-gray-50/50 hover:bg-gray-100"
                        >
                            <td class="px-4 py-3 pl-12 text-gray-700">
                                <i class="bi bi-arrow-return-right mr-2 text-gray-400"></i>
                                <button class="hover:underline" @click="viewingCategory = child">
                                    <span v-html="highlight(child.name)"></span>
                                </button>
                                <span v-if="filters.type === 'child'" class="ml-2 text-xs text-gray-400">
                                    ({{ cat.name }} এর অধীনে)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ child.slug }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                      :class="child.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'">
                                    {{ child.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ child.order }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit(child)" class="p-2 rounded hover:bg-gray-100 text-gray-600" title="সম্পাদনা">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="deletingCategory = child" class="p-2 rounded hover:bg-red-50 text-red-600" title="ডিলিট">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Modals -->
        <CategoryShowModal
            :category="viewingCategory"
            @close="viewingCategory = null"
            @edit="(cat) => { viewingCategory = null; openEdit(cat) }"
        />

        <CategoryFormModal
            :open="showFormModal"
            :editing-category="editingCategory"
            :initial-parent-id="initialParentId"
            :parent-options="parentOptions"
            @close="showFormModal = false"
            @saved="handleSaved"
        />

        <CategoryDeleteModal
            :category="deletingCategory"
            @close="deletingCategory = null"
            @deleted="handleDeleted"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import api from '@/services/api'
import CategoryShowModal from '@/components/admin/category/CategoryShowModal.vue'
import CategoryFormModal from '@/components/admin/category/CategoryFormModal.vue'
import CategoryDeleteModal from '@/components/admin/category/CategoryDeleteModal.vue'
import type { AdminCategory } from '@/types/category'

const categories = ref<AdminCategory[]>([])
const loading = ref(false)
const expanded = ref<Set<number>>(new Set())

const viewingCategory = ref<AdminCategory | null>(null)
const editingCategory = ref<AdminCategory | null>(null)
const deletingCategory = ref<AdminCategory | null>(null)
const showFormModal = ref(false)
const initialParentId = ref<number | null>(null)

const filters = reactive({
    search: '',
    status: '' as '' | 'active' | 'inactive',
    type: '' as '' | 'parent' | 'child',
})

const hasActiveFilters = computed(() =>
    !!filters.search || !!filters.status || !!filters.type
)

const resetFilters = () => {
    filters.search = ''
    filters.status = ''
    filters.type = ''
}

const matchesSearch = (item: AdminCategory) => {
    if (!filters.search.trim()) return true
    const q = filters.search.trim().toLowerCase()
    return item.name.toLowerCase().includes(q) || item.slug.toLowerCase().includes(q)
}

const matchesStatus = (item: AdminCategory) => {
    if (!filters.status) return true
    return filters.status === 'active' ? item.is_active : !item.is_active
}

// Builds the filtered tree: a parent stays visible if it matches directly,
// OR if any of its children match — in which case only the matching children show.
const filteredCategories = computed(() => {
    // "শুধু সাব-ক্যাটাগরি" mode: flatten every matching child under its parent, ignore parent-level matching
    if (filters.type === 'child') {
        return categories.value
            .map((cat) => ({
                ...cat,
                children: cat.children.filter((c) => matchesSearch(c) && matchesStatus(c)),
            }))
            .filter((cat) => cat.children.length > 0)
    }

    return categories.value
        .map((cat) => {
            const parentMatches = matchesSearch(cat) && matchesStatus(cat)
            const matchingChildren = cat.children.filter((c) => matchesSearch(c) && matchesStatus(c))

            if (parentMatches) {
                // Parent itself matches — show it with all its children (edit/expand behaves normally)
                return cat
            }

            if (matchingChildren.length > 0) {
                // Parent doesn't match but has matching children — show parent as a wrapper with only those children
                return { ...cat, children: matchingChildren }
            }

            return null
        })
        .filter((cat): cat is AdminCategory => cat !== null)
})

// Auto-expand any parent whose children matched the search, so results aren't hidden behind a collapsed row
watch(filteredCategories, (list) => {
    if (!filters.search && !filters.status) return
    list.forEach((cat) => {
        if (cat.children.length > 0) expanded.value.add(cat.id)
    })
})

const highlight = (text: string) => {
    if (!filters.search.trim()) return text
    const q = filters.search.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
    return text.replace(new RegExp(`(${q})`, 'ig'), '<mark class="bg-yellow-200 rounded px-0.5">$1</mark>')
}

const parentOptions = computed(() =>
    categories.value.filter((c) => c.id !== editingCategory.value?.id)
)

const toggleExpand = (id: number) => {
    expanded.value.has(id) ? expanded.value.delete(id) : expanded.value.add(id)
}

const fetchCategories = async () => {
    loading.value = true
    try {
        const { data } = await api.get('/admin/categories')
        categories.value = data.data
    } catch (e) {
        console.error('Failed to load categories:', e)
    } finally {
        loading.value = false
    }
}

const openCreate = (parentId: number | null) => {
    editingCategory.value = null
    initialParentId.value = parentId
    showFormModal.value = true
}

const openEdit = (cat: AdminCategory) => {
    editingCategory.value = cat
    initialParentId.value = null
    showFormModal.value = true
}

const handleSaved = () => {
    showFormModal.value = false
    fetchCategories()
}

const handleDeleted = () => {
    deletingCategory.value = null
    fetchCategories()
}

onMounted(() => fetchCategories())
</script>