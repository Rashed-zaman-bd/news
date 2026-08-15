<template>
  <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-2xl shadow-lg border border-slate-100">
    <h2 class="text-2xl font-bold text-slate-800 mb-2">নতুন পাসওয়ার্ড সেট করুন</h2>
    <p class="text-sm text-slate-500 mb-6">
      আপনার অ্যাকাউন্টের জন্য একটি শক্তিশালী নতুন পাসওয়ার্ড প্রবেশ করুন।
    </p>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="mb-4 p-4 rounded-xl bg-red-50 text-red-600 text-sm border border-red-200">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="handleResetPassword" class="space-y-4">
      <!-- Hidden or Readonly Email Field -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">ইমেল</label>
        <input
          v-model="form.email"
          type="email"
          readonly
          class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100 text-sm text-slate-500 cursor-not-allowed outline-none"
        />
      </div>

      <!-- Password Field -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">নতুন পাসওয়ার্ড</label>
        <input
          v-model="form.password"
          type="password"
          required
          placeholder="নূন্যতম ৮ অক্ষর"
          class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"
          :class="{ 'border-red-500': errors.password }"
        />
        <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password[0] }}</p>
      </div>

      <!-- Password Confirmation Field -->
      <div>
        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">পাসওয়ার্ড নিশ্চিত করুন</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          required
          placeholder="পাসওয়ার্ড পুনরায় লিখুন"
          class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"
        />
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 text-white font-semibold text-sm hover:from-red-700 hover:to-rose-700 focus:ring-4 focus:ring-red-500/25 shadow-lg shadow-red-500/20 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
      >
        <span v-if="isLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
        <span>{{ isLoading ? 'সংরক্ষণ হচ্ছে...' : 'পাসওয়ার্ড পরিবর্তন করুন' }}</span>
      </button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();

const isLoading = ref(false);
const errorMessage = ref('');
const errors = ref({});

const form = reactive({
  token: '',
  email: '',
  password: '',
  password_confirmation: ''
});

// Extract token and email from URL parameters
onMounted(() => {
  form.token = route.query.token || '';
  form.email = route.query.email || '';

  if (!form.token || !form.email) {
    errorMessage.value = 'অবৈধ অথবা মেয়াদোত্তীর্ণ পাসওয়ার্ড রিসেট লিংক।';
  }
});

const handleResetPassword = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  errors.value = {};

  try {
    const response = await api.post('/reset-password', form);

    await Swal.fire({
      icon: 'success',
      title: 'সফল!',
      text: response.data.message || 'আপনার পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।',
      confirmButtonColor: '#DC2626'
    });

    // Redirect to login view upon successful reset
    router.push({ name: 'login' });

  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {};
    } else {
      errorMessage.value = error.response?.data?.message || 'একটি সমস্যা তৈরি হয়েছে, আবার চেষ্টা করুন।';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>