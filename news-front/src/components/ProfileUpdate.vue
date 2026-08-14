<template>
  <div class="max-w-4xl mx-auto px-4 py-10">
    <!-- Main Card container -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-100/80 overflow-hidden transition-all">
      
      <!-- Premium Glass/Gradient Cover Banner -->
      <div class="relative h-44 bg-gradient-to-r from-slate-900 via-slate-800 to-red-950 p-6 flex flex-col justify-between overflow-hidden">
        <!-- Abstract Background Glows -->
        <div class="absolute -right-10 -top-10 w-60 h-60 bg-red-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 bottom-0 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Top Badge -->
        <div class="relative z-10 flex justify-between items-center">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-white backdrop-blur-md border border-white/15">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            অ্যাকাউন্ট সেটিংস
          </span>
        </div>

        <!-- Banner Text -->
        <div class="relative z-10">
          <h1 class="text-2xl font-bold text-white tracking-wide">প্রোফাইল সেটিংস</h1>
          <p class="text-xs text-slate-300 mt-0.5 font-light">আপনার ব্যক্তিগত তথ্য এবং সিকিউরিটি পছন্দসমূহ আপডেট করুন</p>
        </div>
      </div>

      <!-- Avatar Overlay Section -->
      <div class="relative mt-12 px-6 md:px-10 pb-6 border-b border-slate-100 bg-slate-50/50">
        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 -mt-16">
          
          <!-- Avatar Container -->
          <div class="relative group">
            <div class="w-32 h-32 rounded-2xl overflow-hidden ring-4 ring-white shadow-xl bg-slate-200">
              <img
                :src="avatarPreview || defaultAvatar"
                alt="Profile Avatar"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              />
            </div>

            <!-- Upload Floating Button -->
            <label
              for="avatar-upload"
              class="absolute -bottom-2 -right-2 bg-gradient-to-r from-red-600 to-rose-600 text-white p-3 rounded-xl cursor-pointer shadow-lg hover:shadow-red-500/30 hover:scale-105 transition-all duration-200 border-2 border-white flex items-center justify-center"
              title="ছবি পরিবর্তন করুন"
            >
              <i class="bi bi-camera-fill text-sm"></i>
              <input
                id="avatar-upload"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                class="hidden"
                @change="handleAvatarChange"
              />
            </label>
          </div>

          <!-- Quick Info -->
          <div class="text-center sm:text-left space-y-1 pb-2">
            <div class="flex items-center justify-center sm:justify-start gap-2">
              <h2 class="text-xl font-bold text-slate-800">{{ form.name || 'User Name' }}</h2>
              <span v-if="form.designation" class="px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 border border-red-100">
                {{ form.designation }}
              </span>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ form.email || 'user@example.com' }}</p>
            <p class="text-xs text-slate-400">JPG, PNG, অথবা WEBP (সর্বোচ্চ: 2MB)</p>
            <p v-if="errors.avatar" class="text-xs font-semibold text-red-500 mt-1">{{ errors.avatar[0] }}</p>
          </div>
        </div>
      </div>

      <!-- Main Form Area -->
      <form @submit.prevent="handleUpdate" class="p-6 md:p-10 space-y-10">
        
        <!-- Section 1: Personal Details -->
        <div class="space-y-6">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm">
              <i class="bi bi-person-vcard"></i>
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-800">ব্যক্তিগত তথ্য</h3>
              <p class="text-xs text-slate-400">আপনার সাধারণ প্রোফাইল এনট্রিগুলো আপডেট করুন</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                নাম <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="bi bi-person text-base"></i>
                </span>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="আপনার নাম লিখুন"
                  class="w-full pl-10 pr-4 py-3 rounded-xl border bg-slate-50/50 text-sm text-slate-800 transition-all duration-200 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none"
                  :class="errors.name ? 'border-red-500 bg-red-50/20' : 'border-slate-200'"
                />
              </div>
              <p v-if="errors.name" class="text-xs text-red-500 pl-1">{{ errors.name[0] }}</p>
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                ইমেল অ্যাড্রেস <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="bi bi-envelope text-base"></i>
                </span>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="example@mail.com"
                  class="w-full pl-10 pr-4 py-3 rounded-xl border bg-slate-50/50 text-sm text-slate-800 transition-all duration-200 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none"
                  :class="errors.email ? 'border-red-500 bg-red-50/20' : 'border-slate-200'"
                />
              </div>
              <p v-if="errors.email" class="text-xs text-red-500 pl-1">{{ errors.email[0] }}</p>
            </div>

            <!-- Phone -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                ফোন নম্বর
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="bi bi-telephone text-base"></i>
                </span>
                <input
                  v-model="form.phone"
                  type="text"
                  placeholder="01XXXXXXXXX"
                  class="w-full pl-10 pr-4 py-3 rounded-xl border bg-slate-50/50 text-sm text-slate-800 transition-all duration-200 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none"
                  :class="errors.phone ? 'border-red-500 bg-red-50/20' : 'border-slate-200'"
                />
              </div>
              <p v-if="errors.phone" class="text-xs text-red-500 pl-1">{{ errors.phone[0] }}</p>
            </div>

            <!-- Designation -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                পদবী (Designation)
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="bi bi-briefcase text-base"></i>
                </span>
                <input
                  v-model="form.designation"
                  type="text"
                  placeholder="Software Engineer / Web Developer"
                  class="w-full pl-10 pr-4 py-3 rounded-xl border bg-slate-50/50 text-sm text-slate-800 transition-all duration-200 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none"
                  :class="errors.designation ? 'border-red-500 bg-red-50/20' : 'border-slate-200'"
                />
              </div>
              <p v-if="errors.designation" class="text-xs text-red-500 pl-1">{{ errors.designation[0] }}</p>
            </div>
          </div>

          <!-- Bio -->
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
              বায়ো (Bio)
            </label>
            <textarea
              v-model="form.bio"
              rows="3"
              placeholder="আপনার অভিজ্ঞতা বা নিজের সম্পর্কে সংক্ষেপে বলুন..."
              class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-800 transition-all duration-200 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none resize-none"
              :class="errors.bio ? 'border-red-500 bg-red-50/20' : 'border-slate-200'"
            ></textarea>
            <p v-if="errors.bio" class="text-xs text-red-500 pl-1">{{ errors.bio[0] }}</p>
          </div>
        </div>

        <div class="h-px bg-slate-100"></div>

        <!-- Section 2: Security / Password -->
        <div class="space-y-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                <i class="bi bi-shield-lock"></i>
              </div>
              <div>
                <h3 class="text-base font-bold text-slate-800">নিরাপত্তা ও পাসওয়ার্ড</h3>
                <p class="text-xs text-slate-400">পরিবর্তন করতে না চাইলে ঘরগুলো ফাঁকা রাখুন</p>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- New Password -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                নতুন পাসওয়ার্ড
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="bi bi-key text-base"></i>
                </span>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="নূন্যতম ৮ অক্ষর"
                  class="w-full pl-10 pr-10 py-3 rounded-xl border bg-slate-50/50 text-sm text-slate-800 transition-all duration-200 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none"
                  :class="errors.password ? 'border-red-500 bg-red-50/20' : 'border-slate-200'"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                >
                  <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
              </div>
              <p v-if="errors.password" class="text-xs text-red-500 pl-1">{{ errors.password[0] }}</p>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                পাসওয়ার্ড নিশ্চিত করুন
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="bi bi-shield-check text-base"></i>
                </span>
                <input
                  v-model="form.password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="পুনরায় একই পাসওয়ার্ড লিখুন"
                  class="w-full pl-10 pr-4 py-3 rounded-xl border bg-slate-50/50 text-sm text-slate-800 transition-all duration-200 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none border-slate-200"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Sticky / Floating Action Bar -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
          <span class="text-xs text-slate-400 order-2 sm:order-1 text-center sm:text-left">
            <i class="bi bi-info-circle mr-1"></i> আপডেট করার পর স্থানীয় ডাটা স্বয়ংক্রিয়ভাবে সিঙ্ক হবে
          </span>

          <div class="flex items-center gap-3 w-full sm:w-auto order-1 sm:order-2">
            <button
              type="button"
              @click="fetchUserProfile"
              :disabled="isLoading"
              class="w-1/2 sm:w-auto px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition-colors disabled:opacity-50"
            >
              রিসেট
            </button>

            <button
              type="submit"
              :disabled="isLoading"
              class="w-1/2 sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 text-white font-semibold text-sm hover:from-red-700 hover:to-rose-700 focus:ring-4 focus:ring-red-500/25 shadow-lg shadow-red-500/20 active:scale-95 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
              <i v-else class="bi bi-check2-circle text-base"></i>
              <span>{{ isLoading ? 'সংরক্ষণ হচ্ছে...' : 'সংরক্ষণ করুন' }}</span>
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import api from '@/services/api';
import Swal from 'sweetalert2';

interface UserProfile {
  id: number;
  name: string;
  email: string;
  phone?: string | null;
  designation?: string | null;
  bio?: string | null;
  avatar?: string | null;
}

const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=0F172A&color=fff&size=128';

const isLoading = ref(false);
const showPassword = ref(false);
const errors = ref<Record<string, string[]>>({});
const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);

const form = reactive({
  name: '',
  email: '',
  phone: '',
  designation: '',
  bio: '',
  password: '',
  password_confirmation: ''
});

// Fetch latest profile state from API
const fetchUserProfile = async () => {
  try {
    const response = await api.get('/me');
    const user: UserProfile = response.data.user;

    form.name = user.name || '';
    form.email = user.email || '';
    form.phone = user.phone || '';
    form.designation = user.designation || '';
    form.bio = user.bio || '';
    form.password = '';
    form.password_confirmation = '';

    if (user.avatar) {
      avatarPreview.value = user.avatar;
    }
  } catch (error) {
    console.error('Profile fetch failed:', error);
  }
};

// Handle avatar image select
const handleAvatarChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    
    // Client-side file size validation (2MB)
    if (file.size > 2 * 1024 * 1024) {
      errors.value = { avatar: ['ফাইল সাইজ সর্বোচ্চ ২MB হতে পারবে।'] };
      return;
    }

    avatarFile.value = file;
    avatarPreview.value = URL.createObjectURL(file);
    errors.value.avatar = [];
  }
};

// Submit Profile Update
const handleUpdate = async () => {
  isLoading.value = true;
  errors.value = {};

  try {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('email', form.email);
    
    if (form.phone) formData.append('phone', form.phone);
    if (form.designation) formData.append('designation', form.designation);
    if (form.bio) formData.append('bio', form.bio);
    if (form.password) {
      formData.append('password', form.password);
      formData.append('password_confirmation', form.password_confirmation);
    }
    if (avatarFile.value) {
      formData.append('avatar', avatarFile.value);
    }

    const response = await api.post('/me', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    const updatedUser = response.data.user;

    // Sync localStorage
    localStorage.setItem('user', JSON.stringify(updatedUser));
    
    // Broadcast event to header/navbar component
    window.dispatchEvent(new Event('auth-changed'));

    // Reset password fields
    form.password = '';
    form.password_confirmation = '';
    avatarFile.value = null;

    Swal.fire({
      icon: 'success',
      title: response.data.message || 'প্রোফাইল সফলভাবে আপডেট করা হয়েছে।',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2000,
      background: '#0F172A',
      color: '#ffffff',
    });

  } catch (error: any) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {};
    } else {
      Swal.fire({
        icon: 'error',
        title: 'ত্রুটি',
        text: error.response?.data?.message || 'একটি সমস্যা হয়েছে! আবার চেষ্টা করুন।',
      });
    }
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchUserProfile();
});
</script>