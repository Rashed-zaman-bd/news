<template>
  <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-2xl shadow-lg border border-slate-100">
    <h2 class="text-2xl font-bold text-slate-800 mb-2">পাসওয়ার্ড ভুলে গেছেন?</h2>
    <p class="text-sm text-slate-500 mb-6">
      আপনার অ্যাকাউন্ট ইমেল প্রবেশ করুন। আমরা পাসওয়ার্ড রিসেট লিংক পাঠাব।
    </p>

    <!-- Success Feedback Alert -->
    <div v-if="successMessage" class="mb-4 p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm border border-emerald-200">
      {{ successMessage }}
    </div>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="mb-4 p-4 rounded-xl bg-red-50 text-red-600 text-sm border border-red-200">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="handleSendLink" class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">ইমেল এড্রেস</label>
        <input
          v-model="email"
          type="email"
          required
          placeholder="yourname@example.com"
          class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"
          :class="{ 'border-red-500': errors.email }"
        />
        <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email[0] }}</p>
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 text-white font-semibold text-sm hover:from-red-700 hover:to-rose-700 focus:ring-4 focus:ring-red-500/25 shadow-lg shadow-red-500/20 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
      >
        <span v-if="isLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
        <span>{{ isLoading ? 'পাঠানো হচ্ছে...' : 'রিসেট লিংক পাঠান' }}</span>
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/services/api'; // Your Axios instance with baseURL configured

const email = ref('');
const isLoading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const errors = ref({});

const handleSendLink = async () => {
  isLoading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  errors.value = {};

  try {
    const response = await api.post('/forgot-password', {
      email: email.value
    });

    successMessage.value = response.data.message || 'পাসওয়ার্ড রিসেট লিংক আপনার ইমেলে পাঠানো হয়েছে!';
    email.value = '';
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