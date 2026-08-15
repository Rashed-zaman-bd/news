<template>
    <div class="flex flex-col justify-center items-center bg-gray-100 p-4 min-h-screen">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <img src="/images/khobor red logo.png" alt="Logo" class="w-72 md:h-24 object-contain">
        </div>

        <!-- Register Card -->
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">
                রেজিস্টার
            </h2>

            <form @submit.prevent="handleRegister" class="space-y-4" novalidate>
                <!-- Name -->
                <div>
                    <label class="block text-base font-medium mb-1">
                        নাম <span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        placeholder="আপনার নাম"
                        @blur="validateField('name')"
                        @input="clearFieldError('name')"
                        :class="[
                            'w-full rounded-md border px-3 py-2 outline-none transition',
                            errors.name ? 'border-red-500 focus:ring-2 focus:ring-red-200' : 'focus:ring-2 focus:ring-blue-300'
                        ]"
                    >
                    <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                </div>

                <!-- Mobile -->
                <div>
                    <label class="block text-base font-medium mb-1">
                        মোবাইল নম্বর<span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.phone" 
                        type="text" 
                        maxlength="11" 
                        placeholder="017XXXXXXXX"
                        @blur="validateField('phone')"
                        @input="clearFieldError('phone')"
                        :class="[
                            'w-full rounded-md border px-3 py-2 outline-none transition',
                            errors.phone ? 'border-red-500 focus:ring-2 focus:ring-red-200' : 'focus:ring-2 focus:ring-blue-300'
                        ]"
                    >
                    <p v-if="errors.phone" class="text-red-500 text-sm mt-1">{{ errors.phone }}</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-base font-medium mb-1">
                        ইমেইল <span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.email" 
                        type="email" 
                        placeholder="example@email.com"
                        @blur="validateField('email')"
                        @input="clearFieldError('email')"
                        :class="[
                            'w-full rounded-md border px-3 py-2 outline-none transition',
                            errors.email ? 'border-red-500 focus:ring-2 focus:ring-red-200' : 'focus:ring-2 focus:ring-blue-300'
                        ]"
                    >
                    <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-base font-medium mb-1">
                        পাসওয়ার্ড <span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.password" 
                        type="password" 
                        placeholder="********"
                        @blur="validateField('password')"
                        @input="clearFieldError('password')"
                        :class="[
                            'w-full rounded-md border px-3 py-2 outline-none transition',
                            errors.password ? 'border-red-500 focus:ring-2 focus:ring-red-200' : 'focus:ring-2 focus:ring-blue-300'
                        ]"
                    >
                    <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
                </div>

                <!-- Profile Image -->
                <div>
                    <label class="block text-base font-medium mb-1">
                        প্রোফাইল ছবি
                    </label>
                    <input 
                        type="file" 
                        accept="image/jpeg,image/jpg,image/png,image/webp"
                        @change="handleFileChange"
                        class="w-full border rounded-md px-3 py-2 text-base text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-base file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p v-if="errors.avatar" class="text-red-500 text-sm mt-1">{{ errors.avatar }}</p>

                    <!-- Preview -->
                    <div v-if="avatarPreview" class="mt-3 flex justify-center">
                        <img 
                            :src="avatarPreview" 
                            alt="Profile Preview"
                            class="w-24 h-24 rounded-full object-cover border shadow-sm"
                        >
                    </div>
                </div>

                <!-- Register Button -->
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 rounded-md transition disabled:opacity-50 flex items-center justify-center mt-2"
                >
                    <span v-if="loading">
                        <i class="bi bi-arrow-repeat animate-spin mr-2"></i>
                        রেজিস্ট্রেশন হচ্ছে...
                    </span>
                    <span v-else>
                        রেজিস্ট্রেশন
                    </span>
                </button>

                <!-- Login Link -->
                <div class="text-center text-base text-gray-600">
                    যদি একাউন্ট থাকে
                    <router-link to="/login" class="text-blue-600 underline hover:font-semibold ml-1">
                        লগইন
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import Swal from 'sweetalert2';

const router = useRouter();

const form = reactive({
    name: '',
    phone: '',
    email: '',
    password: '',
    avatar: null as File | null,
});

// Reactive object to track validation errors for each field
const errors = reactive({
    name: '',
    phone: '',
    email: '',
    password: '',
    avatar: '',
});

const avatarPreview = ref<string | null>(null);
const loading = ref(false);

// Success Toast Configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    background: '#16a34a',
    color: '#ffffff',
    iconColor: '#ffffff',
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Clear single error on user input
const clearFieldError = (field: keyof typeof errors) => {
    errors[field] = '';
};

// Field Validation Logic (matching Laravel FormRequest rules)
const validateField = (field: keyof typeof errors) => {
    switch (field) {
        case 'name':
            if (!form.name.trim()) {
                errors.name = 'নাম প্রদান করা আবশ্যক।';
            } else if (form.name.length > 255) {
                errors.name = 'নাম ২৫৫ অক্ষরের বেশি হতে পারবে না।';
            } else {
                errors.name = '';
            }
            break;

        case 'email':
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!form.email.trim()) {
                errors.email = 'ইমেইল প্রদান করা আবশ্যক।';
            } else if (!emailRegex.test(form.email)) {
                errors.email = 'একটি বৈধ ইমেইল ঠিকানা লিখুন।';
            } else {
                errors.email = '';
            }
            break;

        case 'phone':
            // Phone is nullable, but if entered, must be valid BD format
            const phoneRegex = /^01[3-9]\d{8}$/;
            if (form.phone && !phoneRegex.test(form.phone)) {
                errors.phone = 'ফোন নম্বরটি অবশ্যই ০১৩-০১৯ দিয়ে শুরু হওয়া একটি বৈধ ১১-সংখ্যার বাংলাদেশী নম্বর হতে হবে।';
            } else {
                errors.phone = '';
            }
            break;

        case 'password':
            if (!form.password) {
                errors.password = 'পাসওয়ার্ড প্রদান করা আবশ্যক।';
            } else if (form.password.length < 8) {
                errors.password = 'পাসওয়ার্ড অন্তত ৮ অক্ষরের হতে হবে।';
            } else {
                errors.password = '';
            }
            break;
    }
};

// Validate all fields on submit
const validateForm = (): boolean => {
    validateField('name');
    validateField('email');
    validateField('phone');
    validateField('password');

    return !errors.name && !errors.email && !errors.phone && !errors.password && !errors.avatar;
};

// File upload handler with validation
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    errors.avatar = '';

    if (target.files && target.files[0]) {
        const file = target.files[0];
        
        // Validate MIME type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            errors.avatar = 'ছবিটি অবশ্যই jpeg, png, jpg বা webp ফরম্যাটের ফাইল হতে হবে।';
            return;
        }

        // Validate File Size (Max 2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            errors.avatar = 'ছবিটির সাইজ সর্বোচ্চ ২ মেগাবাইট (2MB) হতে পারবে।';
            return;
        }

        form.avatar = file;
        
        // Local preview render
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const handleRegister = async () => {
    // Run frontend validation check
    if (!validateForm()) {
        return;
    }

    loading.value = true;

    try {
        const formData = new FormData();
        formData.append('name', form.name);
        formData.append('email', form.email);
        formData.append('password', form.password);
        if (form.phone) {
            formData.append('phone', form.phone);
        }
        if (form.avatar) {
            formData.append('avatar', form.avatar);
        }

        const response = await api.post('/register', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        // Inside handleRegister method in Register.vue

        if (response.data.access_token) {
            localStorage.setItem('apiToken', response.data.access_token);
        }
        if (response.data.user) {
            localStorage.setItem('user', JSON.stringify(response.data.user));
        }

        // 💥 DISPATCH EVENT TO NOTIFY Header / UserAccount
        window.dispatchEvent(new Event('auth-changed'));

        Toast.fire({
            icon: 'success',
            title: response.data.message || 'রেজিস্ট্রেশন সফল হয়েছে!'
        });

        setTimeout(() => {
            router.push('/');
        }, 800);

    } catch (error: any) {
        // Handle server-side errors (e.g. email/phone unique constraints from DB)
        const serverErrors = error.response?.data?.errors;

        if (serverErrors && typeof serverErrors === 'object') {
            // Map backend validation errors directly to frontend error fields if they match
            Object.keys(serverErrors).forEach((key) => {
                if (key in errors) {
                    errors[key as keyof typeof errors] = serverErrors[key][0];
                }
            });
        } else {
            const message = error.response?.data?.message || 'কিছু একটা ভুল হয়েছে। আবার চেষ্টা করুন।';
            Swal.fire({
                icon: 'error',
                title: 'রেজিস্ট্রেশন ব্যর্থ হয়েছে',
                text: message,
                confirmButtonText: 'ঠিক আছে',
                confirmButtonColor: '#4B5563'
            });
        }
    } finally {
        loading.value = false;
    }
};
</script>