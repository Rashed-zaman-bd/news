<template>
    <div class="max-w-7xl mx-auto px-4 bg-white text-gray-900 border-b border-gray-200 pt-4 pb-4">

        <!-- Main 12-Column Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left Section: News Cards + YouTube Banner (8 Cols) -->
            <div class="lg:col-span-9 space-y-6 lg:border-r lg:border-gray-200 lg:pr-6">

                <!-- 4-Column News Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">

                    <!-- Article 1 -->
                    <article class="space-y-2 border-b sm:border-b-0 sm:border-r border-gray-200 pb-3 sm:pb-0 sm:pr-3">
                        <div class="aspect-[16/10] w-full overflow-hidden rounded bg-gray-100">
                            <img src="/images/topsideimage.png" alt=""
                                class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
                        </div>
                        <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
                            <span>ঘণ্টায় সর্বোচ্চ ৬০ কিমি বেগে ঝড়ের পূর্বাভাস</span>
                        </h3>
                    </article>

                    <!-- Article 2 -->
                    <article class="space-y-2 border-b sm:border-b-0 sm:border-r border-gray-200 pb-3 sm:pb-0 sm:pr-3">
                        <div class="aspect-[16/10] w-full overflow-hidden rounded bg-gray-100">
                            <img src="/images/topsideimage.png" alt=""
                                class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
                        </div>
                        <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
                            <span>কর্মক্ষেত্রে জেনজি— দক্ষতার চেয়ে বড় বাধা মানসিক স্বাস্থ্য</span>
                        </h3>
                    </article>

                    <!-- Article 3 -->
                    <article class="space-y-2 border-b sm:border-b-0 sm:border-r border-gray-200 pb-3 sm:pb-0 sm:pr-3">
                        <div class="aspect-[16/10] w-full overflow-hidden rounded bg-gray-100">
                            <img src="/images/topsideimage.png" alt=""
                                class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" />
                        </div>
                        <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
                            <span>ক্ষুধার্ত ডাইনোসর সামলে তৃতীয় সপ্তাহেও এক নম্বরে ‘ব্র্যান্ড নিউ ডে’</span>
                        </h3>
                    </article>

                    <!-- Article 4 -->
                    <article class="space-y-2 pb-3 sm:pb-0">
                        <div class="aspect-[16/10] w-full overflow-hidden rounded bg-gray-100">
                            <img src="/images/topsideimage.png" alt=""
                                class="w-full h-full object-cover cursor-pointer transition-transform duration-500 ease-out hover:scale-105 will-change-transform" />
                        </div>
                        <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
                            <span>এপিবিএন সদস্যদের তথ্য গায়েব, বিচার নিশ্চিত করা নিয়ে শঙ্কা</span>
                        </h3>
                    </article>

                </div>

                <!-- Red YouTube Subscription Banner -->
                <div class="bg-red-700 text-white rounded p-3 flex items-center justify-between cursor-pointer shadow">
                    <div class="flex items-center space-x-3">
                        <!-- YouTube Icon -->
                        <div class="bg-white text-red-600 rounded-lg p-2 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                            </svg>
                        </div>
                        <span class="font-bold text-lg md:text-xl">নিয়মিত ভিডিও পেতে সাবস্ক্রাইব করুন</span>
                    </div>
                    <div class="text-white font-bold text-2xl pr-4">|</div>
                </div>

            </div>

            <!-- Right Section: Sidebar Ads (4 Cols) -->
            <div class="lg:col-span-3 space-y-4">

                <!-- Top Ad Banner -->
                <div class="w-full rounded overflow-hidden shadow-sm">
                    <img src="/images/ad-topbottom-right.png" alt=""
                        class="w-full h-auto object-cover cursor-pointer" />
                </div>

                <!-- Bottom Ad Banner -->
                <div class="w-full rounded overflow-hidden shadow-sm">
                    <img src="/images/ad-topbottom-rightbottom.png" alt=""
                        class="w-full h-auto object-cover cursor-pointer" />
                </div>

            </div>

        </div>

    </div>

</template>

<script setup lang="ts">
</script>