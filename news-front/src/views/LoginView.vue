<template>
    <div class="flex flex-col justify-center items-center bg-gray-100 p-4 min-h-screen">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <img src="/images/khobor red logo.png" alt="Logo" class="w-72 md:h-24 object-contain">
        </div>

        <!-- Login Card -->
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">
                লগইন
            </h2>

            <form @submit.prevent="handleLogin" class="space-y-5">
                <!-- Mobile -->
                <div>
                    <label class="block text-base font-medium mb-2">
                        মোবাইল নম্বর
                    </label>
                    <input 
                        v-model="form.phone"
                        type="text" 
                        maxlength="11" 
                        placeholder="017XXXXXXXX"
                        required
                        class="w-full rounded-md border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-base font-medium mb-2">
                        পাসওয়ার্ড
                    </label>
                    <input 
                        v-model="form.password"
                        type="password" 
                        placeholder="********"
                        required
                        class="w-full rounded-md border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>

                <div class="text-right text-base text-gray-600">
                    <router-link to="/forgot-password" class="text-red-400 hover:font-semibold ml-1">
                        পাসওয়ার্ড ভুলে গেছেন?
                    </router-link>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    :disabled="loading"
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 rounded-md transition disabled:opacity-50 flex items-center justify-center"
                >
                    <span v-if="loading">
                        <i class="bi bi-arrow-repeat animate-spin mr-2"></i>
                        লগইন হচ্ছে...
                    </span>
                    <span v-else>
                        লগইন
                    </span>
                </button>

                <!-- Register Navigation -->
                <div class="text-center text-base text-gray-600">
                    যদি একাউন্ট না থাকে
                    <router-link to="/register" class="text-gray-700 underline hover:font-semibold ml-1">
                        রেজিস্টার করুন
                    </router-link>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full">
                    <button type="button" class="w-full sm:w-1/2 flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-700 text-white py-2 px-1 rounded-md transition opacity-50 cursor-not-allowed">
                        <i class="bi bi-envelope-at text-lg"></i>
                        <span>জিমেইলে একাউন্ট করুন</span>
                    </button>

                    <button type="button" class="w-full sm:w-1/2 flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-700 text-white py-2 px-1 rounded-md transition opacity-50 cursor-not-allowed">
                        <i class="bi bi-facebook text-lg"></i>
                        <span>ফেসবুকে একাউন্ট করুন</span>
                    </button>
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
    phone: '',
    password: ''
});

const loading = ref(false);

// Success Toast configuration (Top-Right Green)
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    background: '#16a34a', // Tailwind green-600 (or use '#10B981' for emerald)
    color: '#ffffff',       // White text
    iconColor: '#ffffff',   // White icon
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

const handleLogin = async () => {
    loading.value = true;

    try {
        const response = await api.post('/login', form);
        
        // Save auth data
        localStorage.setItem('apiToken', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        // Show Top-Right Flash Toast on Success
        Toast.fire({
            icon: 'success',
            title: response.data.message || 'লগইন সফল হয়েছে!'
        });

        // Delay redirect briefly to let toast appear
        setTimeout(() => {
            router.push('/');
        }, 800);

    } catch (error) {
        let message = 'কিছু একটা ভুল হয়েছে। আবার চেষ্টা করুন।';

       
        // Show Error Popup Modal
        Swal.fire({
            icon: 'error',
            title: 'লগইন ব্যর্থ হয়েছে',
            text: message,
            confirmButtonText: 'ঠিক আছে',
            confirmButtonColor: '#4B5563'
        });
    } finally {
        loading.value = false;
    }
};
</script>