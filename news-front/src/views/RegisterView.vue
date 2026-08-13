<template>
    <div class="flex flex-col justify-center items-center bg-gray-100 p-4">
        <!--Logo-->
        <div flex flex-col items-center mb-6>
            <img src="/images/khobor logo.png" alt="Logo" class="w-full h-32 md:w-full md:h-36">
        </div>

        <!--Login Card-->
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">
                রেজিস্টার
            </h2>
            <form action="" class="space-y-5">
                <!--Name-->
                <div>
                    <label for="" class="block text-base font-medium mb-2">
                        নাম
                    </label>

                    <input type="text" placeholder="নাম"
                        class="w-full rounded-md border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>

                <!--Mobile-->
                <div>
                    <label for="" class="block text-base font-medium mb-2">
                        মোবাইল নম্বর
                    </label>

                    <input type="text" maxlength="11" placeholder="০১৭XXXXXXXX"
                        class="w-full rounded-md border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>

                <!--Email-->
                <div>
                    <label for="" class="block text-base font-medium mb-2">
                        ইমেইল
                    </label>

                    <input type="email" placeholder="example@email.com"
                        class="w-full rounded-md border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>

                <!--Password-->
                <div>
                    <label for="" class="block text-base font-medium mb-2">
                        পাসওয়ার্ড
                    </label>
                    <input type="password" placeholder="*********"
                        class="w-full rounded-md border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300"
                    >
                </div>

                <!--Profile Image-->
                <div>
                    <label for="" class="block text-base font-medium mb-2">
                        প্রোফাইল ছবি
                    </label>

                    <input type="file" accept="image/jpeg,image/jpg,image/png,image/webp"
                        class="w-full border rounded-md px-3 py-2 text-base text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-base file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >

                    <!--Preview-->
                    <div class="mt-3" flex justify-center>
                        <img src="" alt="Profile Preview"
                            class="w-24 h-24 rounded-full object-cover border shadow-sm"
                        >
                    </div>
                </div>

                <!--Register Button-->
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-gray-500 hover:bg-gray-700 text-white py-2 rounded-md transition disabled:opacity-50"
                >
                    <span v-if="loading">
                        <i class="bi bi-arrow-repeat animate-spin mr-2"></i>
                        রেজিস্ট্রেশন হচ্ছে
                    </span>
                
                    <span v-else>
                        রেজিস্ট্রেশন
                    </span>
                </button>

                <!--Login Link   ----->
                <div class="text-center text-base text-gray-600">
                    যদি একাউন্ট থাকে
                    <router-link to="/login"
                        class="text-blue-600 underline hover:font-semibold ml-1">
                        লগইন
                    </router-link>
                </div>

                
            </form>
        </div>
    </div>
</template>