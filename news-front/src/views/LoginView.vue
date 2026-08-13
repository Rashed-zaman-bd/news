<template>
    <div class="flex flex-col justify-center items-center bg-gray-100 p-4">
        <!--Logo-->
        <div class="flex flex-col items-center mb-6">
            <img src="/images/khobor logo.png" alt="Logo"
            class="w-full h-32 md:w-full md:h-36 "
            >
        </div>
        <!--  Login Card-->
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">
                লগইন
            </h2>
            <form action="" class="space-y-5">
                <!--Mobile-->
                <div>
                    <label for="" class="block text-base font-medium mb-2">
                        মোবাইল নম্বর
                    </label>
                    <input type="text" maxlength="11" placeholder="০১৭XXXXXXXX"
                    class="w-full rounded-md border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <!--Password-->
                <div>
                    <label for="" class="block text-base font-medium mb-2">
                        পাসওয়ার্ড
                    </label>
                    <input type="password" placeholder="********"
                    class="w-full rounded-md border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <!--Buttom-->
                <button type="submit" :disabled="loading"
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 rounded-md transition disabled:opacity-50"
                >
                    <span v-if="loding">
                        <i class="bi bi-arrow-repeat animate-spin mr-2"></i>
                        লগইন হচ্ছে
                    </span>
                    <span v-else>
                        লগইন
                    </span>
                </button>

                <!--Register-->
                <div class="text-center text-base text-gray-600">
                    যদি একাউন্ট না থাকে
                    <router-link to="/register"
                        class="text-gray-700 underline hover:font-semibold ml-1"
                    >
                        রেজিস্টার করুন
                    </router-link>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full">
                    <button class="w-full sm:w-1/2 flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-700 text-white py-2 px-1        rounded-md transition disabled:opacity-50">
                        <i class="bi bi-envelope-at text-lg"></i>
                        <span>জিমেইলে একাউন্ট করুন</span>
                    </button>

                    <button class="w-full sm:w-1/2 flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-700 text-white py-2 px-1 rounded-md transition disabled:opacity-50">
                        <i class="bi bi-facebook text-lg"></i>
                        <span>ফেসবুকে একাউন্ট করুন</span>
                    </button>
                </div>
            </form>
        </div>  

    </div>
</template>