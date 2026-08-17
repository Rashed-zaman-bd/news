<template>
    <div class="max-w-7xl mx-auto px-4 bg-white border-b border-gray-200 pt-4 pb-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- ফিচার: slider column -->
            <div class="lg:col-span-3 flex flex-col gap-3 border-r border-gray-300 p-2">
                <h2 class="font-bold text-lg border-b-2 border-red-600 inline-block pb-1 w-fit">
                    ফিচার
                </h2>

                <div class="relative w-full aspect-[4/3] overflow-hidden rounded-md bg-gray-100">
                    <transition-group name="fade" tag="div" class="absolute inset-0">
                        <img v-for="(slide, i) in featureSlides" v-show="i === activeSlide" :key="slide.src"
                            :src="slide.src" :alt="slide.alt" class="absolute inset-0 w-full h-full object-cover" />
                    </transition-group>

                    <!-- dots -->
                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1.5">
                        <button v-for="(slide, i) in featureSlides" :key="'dot-' + i" @click="activeSlide = i"
                            class="w-2 h-2 rounded-full transition-colors"
                            :class="i === activeSlide ? 'bg-white' : 'bg-white/50'" />
                    </div>
                </div>
            </div>

            <!-- মতামত -->
            <div class="lg:col-span-4 border-r border-gray-300 p-2">
                <h2 class="font-bold text-lg border-b-2 border-red-600 inline-block pb-1 w-fit mb-3">
                    মতামত
                </h2>

                <div>
                    <img src="/images/topimage.png" alt="মতামত"
                        class="float-left w-16 h-16 object-cover rounded-md mr-3 mb-1" />
                    <p class="text-base leading-relaxed text-gray-800">
                        প্রধানমন্ত্রী তারেক রহমান বলেছেন, ‘আমরা ৭১ সালে যুদ্ধ করে দেশ স্বাধীন করেছি।
                        পরবর্তী সময়ে যখনই গণতন্ত্র হুমকির মুখে পড়েছে, জনগণ রাজপথে নেমে এসেছে,
                        আন্দোলন করেছে, বারবার গণতন্ত্রকে ফিরিয়ে নিয়ে এসেছে। এখন হচ্ছে আমাদের
                        দেশ গঠন করার সময়। বাংলাদেশের ২০ কোটি মানুষকে সচেতন থাকতে হবে। এখন হচ্ছে আমাদের
                        দেশ গঠন করার সময়। বাংলাদেশের ২০ কোটি মানুষকে সচেতন থাকতে হবে।
                    </p>
                    <!-- clears the float so the next block in the page doesn't ride up beside the image -->
                    <div class="clear-both"></div>
                </div>
            </div>

            <!-- সর্বাধিক পঠিত -->
            <div class="lg:col-span-5 flex flex-col gap-3 p-2">
                <h2 class="font-bold text-lg border-b-2 border-red-600 inline-block pb-1 w-fit">
                    সর্বাধিক পঠিত
                </h2>
                <ul class="flex flex-col divide-y divide-gray-200">
                    <li v-for="(item, i) in mostRead" :key="i" class="py-2 flex gap-2">
                        <span class="font-bold text-red-600">{{ i + 1 }}</span>
                        <span>{{ item }}</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const featureSlides = [
    { src: '/images/topimage.png', alt: 'Feature 1' },
    { src: '/images/ad-topbottom-right.png', alt: 'Feature 2' },
    { src: '/images/ad-topbottom-rightbottom.png', alt: 'Feature 3' },
]

const mostRead = ['1', '2', '3', '4', '5'] // replace with real titles from API

const activeSlide = ref(0)
let timer: ReturnType<typeof setInterval> | null = null

function nextSlide() {
    activeSlide.value = (activeSlide.value + 1) % featureSlides.length
}
function prevSlide() {
    activeSlide.value =
        (activeSlide.value - 1 + featureSlides.length) % featureSlides.length
}

onMounted(() => {
    timer = setInterval(nextSlide, 5000)
})
onUnmounted(() => {
    if (timer) clearInterval(timer)
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>