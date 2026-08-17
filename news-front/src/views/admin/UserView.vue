<template>
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">ইউজার ম্যানেজমেন্ট</h1>
            <button
                @click="openCreateModal"
                class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition"
            >
                <i class="bi bi-plus-lg"></i>
                নতুন ইউজার
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-4 flex flex-col sm:flex-row gap-3">
            <input
                v-model="filters.search"
                @input="debouncedSearch"
                type="text"
                placeholder="নাম, ইমেইল বা ফোন দিয়ে খুঁজুন..."
                class="flex-1 border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
            >

            <select
                v-model="filters.role"
                @change="fetchUsers(1)"
                class="border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
            >
                <option value="">সব রোল</option>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="author">Author</option>
                <option value="reader">Reader</option>
            </select>

            <select
                v-model="filters.status"
                @change="fetchUsers(1)"
                class="border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
            >
                <option value="">সব স্ট্যাটাস</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>

            <label class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap px-2">
                <input type="checkbox" v-model="filters.trashed" @change="fetchUsers(1)">
                শুধু ডিলিট হওয়া
            </label>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div v-if="loading" class="p-10 text-center text-gray-500">
                <i class="bi bi-arrow-repeat animate-spin text-2xl"></i>
                <p class="mt-2">লোড হচ্ছে...</p>
            </div>

            <div v-else-if="users.length === 0" class="p-10 text-center text-gray-500">
                কোনো ইউজার পাওয়া যায়নি।
            </div>

            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left border-b">
                    <tr>
                        <th class="px-4 py-3">ইউজার</th>
                        <th class="px-4 py-3">ফোন</th>
                        <th class="px-4 py-3">রোল</th>
                        <th class="px-4 py-3">স্ট্যাটাস</th>
                        <th class="px-4 py-3">তৈরি হয়েছে</th>
                        <th class="px-4 py-3 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="u.avatar"
                                    :src="u.avatar"
                                    class="w-9 h-9 rounded-full object-cover border"
                                >
                                <i v-else class="bi bi-person-circle text-2xl text-gray-400"></i>
                                <div>
                                    <p class="font-medium text-gray-800">{{ u.name }}</p>
                                    <p class="text-xs text-gray-500">{{ u.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ u.phone || '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="roleBadge(u.role)">
                                {{ roleLabel(u.role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="statusBadge(u.status)">
                                {{ statusLabel(u.status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ formatDate(u.created_at) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <template v-if="!filters.trashed">
                                    <button
                                        @click="openEditModal(u)"
                                        class="p-2 rounded hover:bg-gray-100 text-gray-600"
                                        title="সম্পাদনা"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button
                                        @click="confirmDelete(u)"
                                        class="p-2 rounded hover:bg-red-50 text-red-600"
                                        title="ডিলিট"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </template>
                                <template v-else>
                                    <button
                                        @click="restoreUser(u)"
                                        class="p-2 rounded hover:bg-emerald-50 text-emerald-600"
                                        title="পুনরুদ্ধার"
                                    >
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="meta && meta.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t">
                <p class="text-sm text-gray-500">
                    মোট {{ meta.total }} জনের মধ্যে {{ meta.from }}–{{ meta.to }} দেখানো হচ্ছে
                </p>
                <div class="flex gap-1">
                    <button
                        v-for="page in meta.last_page"
                        :key="page"
                        @click="fetchUsers(page)"
                        class="px-3 py-1 rounded text-sm"
                        :class="page === meta.current_page ? 'bg-emerald-600 text-white' : 'hover:bg-gray-100 text-gray-600'"
                    >
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Create / Edit Modal -->
        <Transition
            enter-active-class="transition duration-150"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
                @click.self="closeModal"
            >
                <div class="bg-white rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold">
                            {{ editingUser ? 'ইউজার সম্পাদনা' : 'নতুন ইউজার তৈরি' }}
                        </h2>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">নাম *</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
                            >
                            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">ইমেইল *</label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
                            >
                            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">ফোন</label>
                            <input
                                v-model="form.phone"
                                type="text"
                                maxlength="11"
                                class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
                            >
                            <p v-if="errors.phone" class="text-red-500 text-xs mt-1">{{ errors.phone[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                পাসওয়ার্ড {{ editingUser ? '(পরিবর্তন না করলে খালি রাখুন)' : '*' }}
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                :required="!editingUser"
                                class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
                            >
                            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</p>
                        </div>

                        <div v-if="form.password">
                            <label class="block text-sm font-medium mb-1">পাসওয়ার্ড নিশ্চিত করুন *</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                :required="!!form.password"
                                class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
                            >
                            <p v-if="form.password && form.password_confirmation && form.password !== form.password_confirmation"
                            class="text-red-500 text-xs mt-1">
                                পাসওয়ার্ড মিলছে না
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">রোল</label>
                                <select
                                    v-model="form.role"
                                    :disabled="isEditingSelf"
                                    class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300 disabled:bg-gray-100"
                                >
                                    <option value="admin">Admin</option>
                                    <option value="editor">Editor</option>
                                    <option value="author">Author</option>
                                    <option value="reader">Reader</option>
                                </select>
                                <p v-if="isEditingSelf" class="text-xs text-gray-400 mt-1">নিজের রোল পরিবর্তন করা যাবে না</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">স্ট্যাটাস</label>
                                <select
                                    v-model="form.status"
                                    :disabled="isEditingSelf"
                                    class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300 disabled:bg-gray-100"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                                <p v-if="isEditingSelf" class="text-xs text-gray-400 mt-1">নিজের স্ট্যাটাস পরিবর্তন করা যাবে না</p>
                            </div>
                        </div>

                        <div v-if="form.role === 'author'">
                            <label class="block text-sm font-medium mb-1">পদবি (Designation)</label>
                            <input
                                v-model="form.designation"
                                type="text"
                                placeholder="যেমন: Staff Correspondent"
                                class="w-full border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">প্রোফাইল ছবি</label>
                            <input
                                type="file"
                                accept="image/*"
                                @change="handleAvatarChange"
                                class="w-full border rounded-md px-3 py-2 text-sm"
                            >
                        </div>

                        <!-- Preview -->
                        <div class="mt-3 flex flex-col items-center gap-2">
                            <img
                                v-if="avatarPreview"
                                :src="avatarPreview"
                                alt="Profile Preview"
                                class="w-24 h-24 rounded-full object-cover border shadow-sm"
                            >
                            <i v-else class="bi bi-person-circle text-6xl text-gray-300"></i>

                            <button
                                v-if="avatarFile"
                                type="button"
                                @click="removeSelectedAvatar"
                                class="text-xs text-red-500 hover:underline"
                            >
                                নির্বাচিত ছবি সরান
                            </button>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 rounded-md border text-gray-600 hover:bg-gray-50"
                            >
                                বাতিল
                            </button>
                            <button
                                type="submit"
                                :disabled="submitting"
                                class="px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white disabled:opacity-50"
                            >
                                {{ submitting ? 'সংরক্ষণ হচ্ছে...' : 'সংরক্ষণ করুন' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'

interface AdminUser {
    id: number
    name: string
    email: string
    phone: string | null
    role: string
    status: string
    designation: string | null
    avatar: string | null
    created_at: string
}

interface Meta {
    current_page: number
    last_page: number
    total: number
    from: number
    to: number
}

const currentUserId = computed<number | null>(() => {
    const cached = localStorage.getItem('user')
    return cached ? JSON.parse(cached).id : null
})

const users = ref<AdminUser[]>([])
const meta = ref<Meta | null>(null)
const loading = ref(false)
const submitting = ref(false)
const showModal = ref(false)
const editingUser = ref<AdminUser | null>(null)
const errors = ref<Record<string, string[]>>({})
const avatarFile = ref<File | null>(null)

const filters = reactive({
    search: '',
    role: '',
    status: '',
    trashed: false,
})

const emptyForm = () => ({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    role: 'reader',
    status: 'active',
    designation: '',
})

const avatarPreview = ref<string | null>(null);

const form = reactive(emptyForm())

const isEditingSelf = computed(() => editingUser.value?.id === currentUserId.value)

let searchTimeout: ReturnType<typeof setTimeout>
const debouncedSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => fetchUsers(1), 400)
}

const fetchUsers = async (page = 1) => {
    loading.value = true
    try {
        const { data } = await api.get('/admin/users', {
            params: {
                page,
                search: filters.search || undefined,
                role: filters.role || undefined,
                status: filters.status || undefined,
                trashed: filters.trashed ? 1 : undefined,
            },
        })

        users.value = data.data
        meta.value = {
            current_page: data.meta.current_page,
            last_page: data.meta.last_page,
            total: data.meta.total,
            from: data.meta.from,
            to: data.meta.to,
        }
    } catch (e) {
        console.error('Failed to load users:', e)
        Swal.fire('ত্রুটি', 'ইউজার লোড করা যায়নি।', 'error')
    } finally {
        loading.value = false
    }
}

const roleLabel = (role: string) =>
    ({ admin: 'Admin', editor: 'Editor', author: 'Author', reader: 'Reader' }[role] || role)

const roleBadge = (role: string) =>
    ({
        admin: 'bg-purple-100 text-purple-700',
        editor: 'bg-blue-100 text-blue-700',
        author: 'bg-amber-100 text-amber-700',
        reader: 'bg-gray-100 text-gray-600',
    }[role] || 'bg-gray-100 text-gray-600')

const statusLabel = (status: string) =>
    ({ active: 'Active', inactive: 'Inactive', suspended: 'Suspended' }[status] || status)

const statusBadge = (status: string) =>
    ({
        active: 'bg-emerald-100 text-emerald-700',
        inactive: 'bg-gray-100 text-gray-600',
        suspended: 'bg-red-100 text-red-700',
    }[status] || 'bg-gray-100 text-gray-600')

const formatDate = (iso: string) =>
    new Date(iso).toLocaleDateString('bn-BD', { year: 'numeric', month: 'short', day: 'numeric' })

const openCreateModal = () => {
    editingUser.value = null
    Object.assign(form, emptyForm())
    errors.value = {}
    avatarFile.value = null
    revokePreviewIfBlob()
    avatarPreview.value = null
    showModal.value = true
}

const openEditModal = (u: AdminUser) => {
    editingUser.value = u
    Object.assign(form, {
        name: u.name,
        email: u.email,
        phone: u.phone || '',
        password: '',
        role: u.role,
        status: u.status,
        designation: u.designation || '',
    })
    errors.value = {}
    avatarFile.value = null
    revokePreviewIfBlob()
    avatarPreview.value = u.avatar // show their current photo by default
    showModal.value = true
}

const closeModal = () => {
    revokePreviewIfBlob()
    avatarPreview.value = null
    showModal.value = false
}

const handleAvatarChange = (e: Event) => {
    const target = e.target as HTMLInputElement
    const file = target.files?.[0] || null
    avatarFile.value = file

    // Revoke the previous object URL before creating a new one to avoid memory leaks
    if (avatarPreview.value && avatarPreview.value.startsWith('blob:')) {
        URL.revokeObjectURL(avatarPreview.value)
    }

    avatarPreview.value = file ? URL.createObjectURL(file) : (editingUser.value?.avatar || null)
}

const revokePreviewIfBlob = () => {
    if (avatarPreview.value && avatarPreview.value.startsWith('blob:')) {
        URL.revokeObjectURL(avatarPreview.value)
    }
}

const removeSelectedAvatar = () => {
    avatarFile.value = null
    revokePreviewIfBlob()
    avatarPreview.value = editingUser.value?.avatar || null

    // Also clear the native file input so re-selecting the same file re-fires @change
    const input = document.querySelector<HTMLInputElement>('input[type="file"]')
    if (input) input.value = ''
}

const buildFormData = () => {
    const fd = new FormData()
    fd.append('name', form.name)
    fd.append('email', form.email)
    if (form.phone) fd.append('phone', form.phone)
    if (form.password) {
        fd.append('password', form.password)
        fd.append('password_confirmation', form.password_confirmation)
    }
    fd.append('role', form.role)
    fd.append('status', form.status)
    if (form.designation) fd.append('designation', form.designation)
    if (avatarFile.value) fd.append('avatar', avatarFile.value)
    return fd
}

const handleSubmit = async () => {
    submitting.value = true
    errors.value = {}

    try {
        const fd = buildFormData()

        if (editingUser.value) {
            // Laravel needs _method spoofing for multipart PUT requests
            fd.append('_method', 'PUT')
            await api.post(`/admin/users/${editingUser.value.id}`, fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            })
        } else {
            await api.post('/admin/users', fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            })
        }

        await Swal.fire({
            icon: 'success',
            title: editingUser.value ? 'আপডেট সফল হয়েছে' : 'ইউজার তৈরি হয়েছে',
            timer: 1500,
            showConfirmButton: false,
        })

        showModal.value = false
        fetchUsers(meta.value?.current_page || 1)
    } catch (e: any) {
        if (e?.response?.status === 422) {
            errors.value = e.response.data.errors || {}
        } else {
            Swal.fire('ত্রুটি', e?.response?.data?.message || 'সংরক্ষণ ব্যর্থ হয়েছে।', 'error')
        }
    } finally {
        submitting.value = false
    }
}


const confirmDelete = async (u: AdminUser) => {
    const result = await Swal.fire({
        icon: 'warning',
        title: 'আপনি কি নিশ্চিত?',
        text: `"${u.name}" কে ডিলিট করা হবে।`,
        showCancelButton: true,
        confirmButtonText: 'হ্যাঁ, ডিলিট করুন',
        cancelButtonText: 'বাতিল',
        confirmButtonColor: '#dc2626',
    })

    if (!result.isConfirmed) return

    try {
        await api.delete(`/admin/users/${u.id}`)
        Toast('ইউজার ডিলিট হয়েছে')
        fetchUsers(meta.value?.current_page || 1)
    } catch (e: any) {
        Swal.fire('ত্রুটি', e?.response?.data?.message || 'ডিলিট ব্যর্থ হয়েছে।', 'error')
    }
}

const restoreUser = async (u: AdminUser) => {
    try {
        await api.post(`/admin/users/${u.id}/restore`)
        Toast('ইউজার পুনরুদ্ধার হয়েছে')
        fetchUsers(meta.value?.current_page || 1)
    } catch (e: any) {
        Swal.fire('ত্রুটি', e?.response?.data?.message || 'পুনরুদ্ধার ব্যর্থ হয়েছে।', 'error')
    }
}

const Toast = (title: string) => {
    Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        background: '#16a34a',
        color: '#ffffff',
    }).fire({ icon: 'success', title })
}

onMounted(() => fetchUsers())
</script>