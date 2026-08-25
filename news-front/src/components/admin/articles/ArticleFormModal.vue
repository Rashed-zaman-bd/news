<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ isEdit ? 'আর্টিকেল সম্পাদনা' : 'নতুন আর্টিকেল' }}
                    </h3>
                    <button @click="close" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>

                <!-- Body -->
                <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium mb-1">শিরোনাম <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                        >
                        <p v-if="errors.title" class="text-red-500 text-xs mt-1">{{ errors.title }}</p>
                    </div>

                    <!-- Sub title -->
                    <div>
                        <label class="block text-sm font-medium mb-1">সাব-শিরোনাম</label>
                        <input
                            v-model="form.sub_title"
                            type="text"
                            class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                        >
                    </div>

                    <!-- Article Author + Area -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                প্রতিবেদক / লেখক
                            </label>

                            <input
                                v-model="form.article_author"
                                type="text"
                                placeholder="প্রতিবেদকের নাম"
                                class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                প্রতিবেদনের স্থান
                            </label>

                            <input
                                v-model="form.article_area"
                                type="text"
                                placeholder="যেমন: ঢাকা"
                                class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                            />
                        </div>
                    </div>

                    <!-- Category + Status -->
                    <!-- Category + Sub-category + Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">ক্যাটাগরি <span class="text-red-500">*</span></label>
                            <select
                                v-model="form.category_id"
                                required
                                @change="onCategoryChange"
                                class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                            >
                                <option value="" disabled>নির্বাচন করুন</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                            <p v-if="errors.category_id" class="text-red-500 text-xs mt-1">{{ errors.category_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">সাব-ক্যাটাগরি</label>
                            <select
                                v-model="form.sub_category_id"
                                :disabled="!form.category_id || !availableSubCategories.length"
                                class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300 disabled:bg-gray-100"
                            >
                                <option value="">নির্বাচন করুন (ঐচ্ছিক)</option>
                                <option v-for="sub in availableSubCategories" :key="sub.id" :value="sub.id">
                                    {{ sub.name }}
                                </option>
                            </select>
                            <p v-if="errors.sub_category_id" class="text-red-500 text-xs mt-1">{{ errors.sub_category_id }}</p>
                        </div>
                    </div>

                    <!-- Status moves to its own row now that the grid above is full -->
                    <div>
                        <label class="block text-sm font-medium mb-1">স্ট্যাটাস</label>
                        <select
                            v-model="form.status"
                            class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                        >
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <!-- Excerpt -->
                    <div>
                        <label class="block text-sm font-medium mb-1">সংক্ষিপ্তসার</label>
                        <textarea
                            v-model="form.excerpt"
                            rows="2"
                            class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                        ></textarea>
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-medium mb-1">কনটেন্ট <span class="text-red-500">*</span></label>
                        <textarea
                            v-model="form.content"
                            rows="8"
                            required
                            class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                        ></textarea>
                        <p v-if="errors.content" class="text-red-500 text-xs mt-1">{{ errors.content }}</p>
                    </div>

                    <!-- Content Two -->
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            কনটেন্ট - ২
                        </label>

                        <textarea
                            v-model="form.content_two"
                            rows="8"
                            placeholder="দ্বিতীয় অংশের কনটেন্ট লিখুন..."
                            class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                        ></textarea>
                    </div>

                    <!-- Content Three -->
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            কনটেন্ট - ৩
                        </label>

                        <textarea
                            v-model="form.content_three"
                            rows="8"
                            placeholder="তৃতীয় অংশের কনটেন্ট লিখুন..."
                            class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                        ></textarea>
                    </div>

                    <!-- Featured Image -->
                    <div>
                        <label class="block text-sm font-medium mb-1">ফিচার্ড ইমেজ</label>
                        <input
                            type="file"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            @change="handleFileChange"
                            class="w-full border rounded-md px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                        <p v-if="errors.featured_image" class="text-red-500 text-xs mt-1">{{ errors.featured_image }}</p>

                        <div v-if="imagePreview" class="mt-3">
                            <img :src="imagePreview" class="w-32 h-32 object-cover rounded-md border" alt="preview">
                        </div>
                    </div>

                    <!-- Image Information -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                ছবির শিরোনাম
                            </label>

                            <input
                                v-model="form.image_title"
                                type="text"
                                placeholder="ছবির শিরোনাম"
                                class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                ছবির কৃতিত্ব / ফটোগ্রাফার
                            </label>

                            <input
                                v-model="form.image_author"
                                type="text"
                                placeholder="ছবির ফটোগ্রাফারের নাম"
                                class="w-full border rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                            />
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" v-model="form.is_featured" class="rounded">
                            ফিচার্ড আর্টিকেল
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" v-model="form.is_breaking" class="rounded">
                            ব্রেকিং নিউজ
                        </label>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button
                            type="button"
                            @click="close"
                            class="px-4 py-2 rounded-md border text-sm text-gray-600 hover:bg-gray-50"
                        >
                            বাতিল
                        </button>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm disabled:opacity-50 flex items-center gap-2"
                        >
                            <i v-if="submitting" class="bi bi-arrow-repeat animate-spin"></i>
                            {{ isEdit ? 'আপডেট করুন' : 'তৈরি করুন' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue';
import api from '@/services/api';
import Swal from 'sweetalert2';

interface Category {
    id: number;
    name: string;
    parent_id: number | null;
    children?: Category[];
}

interface ArticleData {
    id?: number;
    title: string;
    sub_title?: string | null;
    excerpt?: string | null;
    content: string;
    category_id?: number | string;
    category?: { id: number; name: string } | null;
    sub_category_id?: number | string | null;
    sub_category?: { id: number; name: string } | null;
    status: string;
    is_featured: boolean;
    is_breaking: boolean;
    featured_image?: string | null;

    article_author?: string | null;
    article_area?: string | null;
    image_title?: string | null;
    image_author?: string | null;
    content_two?: string | null;
    content_three?: string | null;
}

const props = defineProps<{
    show: boolean;
    article?: ArticleData | null;
    categories: Category[];
    
}>();

const emit = defineEmits<{
    close: [];
    saved: [];
}>();

const isEdit = computed(() => !!props.article?.id);
const submitting = ref(false);
const imagePreview = ref<string | null>(null);
const imageFile = ref<File | null>(null);

const form = reactive({
    title: '',
    sub_title: '',
    excerpt: '',
    content: '',

    article_author: '',
    article_area: '',
    image_title: '',
    image_author: '',
    content_two: '',
    content_three: '',

    category_id: '' as number | string,
    sub_category_id: '' as number | string,
    status: 'draft',
    is_featured: false,
    is_breaking: false,
});

const errors = reactive({
    title: '',
    category_id: '',
    sub_category_id: '',
    content: '',
    featured_image: '',
});

// Sub-categories belonging to the currently selected parent category
const availableSubCategories = computed(() => {
    if (!form.category_id) return [];
    const parent = props.categories.find((c) => c.id === Number(form.category_id));
    return parent?.children ?? [];
});

const onCategoryChange = () => {
    // Reset sub-category whenever the parent category changes,
    // since the previously selected sub-category may not belong to the new parent
    form.sub_category_id = '';
};

const resetForm = () => {
    form.title = '';
    form.sub_title = '';
    form.excerpt = '';
    form.content = '';

    form.article_author = '';
    form.article_area = '';
    form.image_title = '';
    form.image_author = '';
    form.content_two = '';
    form.content_three = '';

    form.category_id = '';
    form.sub_category_id = '';
    form.status = 'draft';
    form.is_featured = false;
    form.is_breaking = false;

    imagePreview.value = null;
    imageFile.value = null;

    Object.keys(errors).forEach(
        (key) => (errors[key as keyof typeof errors] = '')
    );
};

watch(
    () => props.show,
    (visible) => {
        if (!visible) return;

        if (props.article) {
            form.title = props.article.title;
            form.sub_title = props.article.sub_title ?? '';
            form.excerpt = props.article.excerpt ?? '';
            form.content = props.article.content;

            form.article_author = props.article.article_author ?? '';
            form.article_area = props.article.article_area ?? '';
            form.image_title = props.article.image_title ?? '';
            form.image_author = props.article.image_author ?? '';
            form.content_two = props.article.content_two ?? '';
            form.content_three = props.article.content_three ?? '';

            form.category_id =
                props.article.category_id ??
                props.article.category?.id ??
                '';

            form.sub_category_id =
                props.article.sub_category_id ??
                props.article.sub_category?.id ??
                '';

            form.status = props.article.status ?? 'draft';
            form.is_featured = Boolean(props.article.is_featured);
            form.is_breaking = Boolean(props.article.is_breaking);

            imagePreview.value = props.article.featured_image ?? null;
            imageFile.value = null;
        } else {
            resetForm();
        }
    }
);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    errors.featured_image = '';

    if (target.files && target.files[0]) {
        const file = target.files[0];

        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            errors.featured_image = 'ছবিটি অবশ্যই jpeg, png বা webp ফরম্যাটের হতে হবে।';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            errors.featured_image = 'ছবির সাইজ সর্বোচ্চ ২ মেগাবাইট হতে পারবে।';
            return;
        }

        imageFile.value = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const close = () => {
    emit('close');
};

const handleSubmit = async () => {
    Object.keys(errors).forEach(
        (key) => (errors[key as keyof typeof errors] = '')
    );

    submitting.value = true;

    try {
        const formData = new FormData();

        formData.append('title', form.title);
        formData.append('content', form.content);
        formData.append('category_id', String(form.category_id));
        formData.append('status', form.status);
        formData.append(
            'is_featured',
            form.is_featured ? '1' : '0'
        );
        formData.append(
            'is_breaking',
            form.is_breaking ? '1' : '0'
        );

        // Optional fields
        if (form.sub_category_id) {
            formData.append(
                'sub_category_id',
                String(form.sub_category_id)
            );
        }

        if (form.sub_title) {
            formData.append('sub_title', form.sub_title);
        }

        if (form.excerpt) {
            formData.append('excerpt', form.excerpt);
        }

        if (form.article_author) {
            formData.append(
                'article_author',
                form.article_author
            );
        }

        if (form.article_area) {
            formData.append(
                'article_area',
                form.article_area
            );
        }

        if (form.image_title) {
            formData.append(
                'image_title',
                form.image_title
            );
        }

        if (form.image_author) {
            formData.append(
                'image_author',
                form.image_author
            );
        }

        if (form.content_two) {
            formData.append(
                'content_two',
                form.content_two
            );
        }

        if (form.content_three) {
            formData.append(
                'content_three',
                form.content_three
            );
        }

        if (imageFile.value) {
            formData.append(
                'featured_image',
                imageFile.value
            );
        }

        if (isEdit.value) {
            formData.append('_method', 'PUT');

            await api.post(
                `/admin/articles/${props.article!.id}`,
                formData
            );
        } else {
            await api.post(
                '/admin/articles',
                formData
            );
        }

        Swal.fire({
            icon: 'success',
            title: isEdit.value
                ? 'আপডেট হয়েছে!'
                : 'তৈরি হয়েছে!',
            timer: 1500,
            showConfirmButton: false,
        });

        emit('saved');
        emit('close');

    } catch (error: any) {
        const serverErrors = error.response?.data?.errors;

        if (
            serverErrors &&
            typeof serverErrors === 'object'
        ) {
            Object.keys(serverErrors).forEach((key) => {
                if (key in errors) {
                    errors[key as keyof typeof errors] =
                        serverErrors[key][0];
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ত্রুটি',
                text:
                    error.response?.data?.message ||
                    'কিছু একটা ভুল হয়েছে।',
                confirmButtonColor: '#4B5563',
            });
        }
    } finally {
        submitting.value = false;
    }
};
</script>