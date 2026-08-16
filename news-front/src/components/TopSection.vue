<template>
  <div class="max-w-7xl mx-auto pl-4 pr-4 bg-white text-gray-900 pb-4">
    <!-- Top advert banner -->
    <div class="w-full flex items-center justify-center cursor-pointer p-4 mt-14 sm:mt-0">
      <img src="/images/topbanner.png" alt="Top Banner" />
    </div>

    <!-- Main Section (Lead + Right Column) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pb-6">

      <!-- Main Lead Article (Left / Center Column - 7 Cols) -->
      <article class="lg:col-span-7 flex flex-col space-y-4 border-r-0 lg:border-r border-gray-200 pr-0 lg:pr-6">
        <!-- Infographic / Featured Banner Image -->
        <div class="relative w-full rounded overflow-hidden bg-amber-50 border border-amber-200">
          <img src="/images/topimage.png" class="w-full h-auto object-cover cursor-pointer" alt="Lead Image" />
        </div>

        <!-- Headline & Excerpt -->
        <div class="space-y-2">
          <h4 class="text-sm md:text-lg leading-tight text-gray-900 hover:text-amber-800 cursor-pointer">
            দ্বিমাত্রিক জরিপের তথ্যে মিলছে না কাঙ্ক্ষিত গ্যাস
          </h4>
          <h2 class="text-xl md:text-2xl font-bold leading-snug text-amber-700 cursor-pointer flex items-center gap-1">
            <span>এলএনজি টার্মিনাল বাড়ানোর চেয়ে জরুরি গ্যাসের ত্রিমাত্রিক জরিপ ও রিগে বিনিয়োগ</span>
          </h2>
          <p class="text-gray-600 text-sm leading-relaxed pt-1">
            <!-- Optional Excerpt Content -->
          </p>
        </div>
      </article>

      <!-- Right Column Grid (5 Cols split into 2 Sub-Columns) -->
      <div class="lg:col-span-5 space-y-4">

        <!-- Top Banner Advertisement -->
        <div class="w-full flex items-center justify-center cursor-pointer">
          <img src="/images/top-advertis.png" alt="Sidebar Ad" />
        </div>

        <!-- 2-Column News Feed Below Banner -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

          <!-- News Column A -->
          <div class="space-y-4 border-r-0 md:border-r border-gray-200 pr-0 md:pr-4">
            <!-- Article 1 (With Image) -->
            <article class="space-y-2 border-b border-gray-200 pb-3">
              <div class="aspect-video w-full overflow-hidden rounded bg-gray-100">
                <img src="/images/topsideimage.png" alt="" class="w-full h-full object-cover" />
              </div>
              <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
                <span>ডারউইন টেস্ট : ৫৭ রানের টার্গেট দিল অস্ট্রেলিয়া</span>
              </h3>
            </article>

            <!-- Article 2 (Text Only) -->
            <article class="border-b border-gray-200 pb-3">
              <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
                <span>বাংলাদেশের তালিকার সোয়া ৮ লাখ রোহিঙ্গার মাত্র ৩ লাখকে রাখাইনের ‘বাসিন্দা’ বলছে মিয়ানমার</span>
              </h3>
            </article>

            <!-- Article 3 (Text Only) -->
            <article class="pb-1">
              <h3 class="font-bold text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
                <span>ঘণ্টায় সর্বোচ্চ ৬০ কিমি বেগে ঝড়ের পূর্বাভাস</span>
              </h3>
            </article>
          </div>

          <!-- News Column B (With Ads) -->
          <div class="space-y-4">
            <div class="rounded min-h-[200px] flex flex-col justify-center items-center shadow-inner overflow-hidden">
              <img src="/images/top-leftad.png" alt="Ad 1" />
            </div>

            <div class="rounded min-h-[200px] flex flex-col justify-center items-center shadow-inner overflow-hidden">
              <img src="/images/top-leftad.png" alt="Ad 2" />
            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- Bottom Section: 4-Column Grid Row (Image + Title) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 border-b border-gray-200 pb-6">
  
  <!-- Card 1 -->
  <article class="flex flex-row items-start gap-3 border-b sm:border-b-0 border-gray-200 pb-4 sm:pb-0">
    <!-- Image Wrapper: Holds border and clips the image zoom -->
    <div class="w-3/5 aspect-video shrink-0 overflow-hidden rounded border-2 border-gray-400 bg-gray-100">
      <img 
        src="/images/topsideimage.png" 
        alt="Article 1" 
        class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" 
      />
    </div>

    <!-- Title: 2/3 width -->
    <h3 class="w-2/5 font-bold text-sm md:text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
      <span>ডারউইন টেস্ট : ৫৭ রানের টার্গেট দিল অস্ট্রেলিয়া</span>
    </h3>
  </article>

  <!-- Card 2 -->
  <article class="flex flex-row items-start gap-3 border-b sm:border-b-0 border-gray-200 pb-4 sm:pb-0">
    <div class="w-3/5 aspect-video shrink-0 overflow-hidden rounded border-2 border-gray-400 bg-gray-100">
      <img 
        src="/images/topsideimage.png" 
        alt="Article 2" 
        class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" 
      />
    </div>
    <h3 class="w-2/5 font-bold text-sm md:text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
      <span>ডারউইন টেস্ট : ৫৭ রানের টার্গেট দিল অস্ট্রেলিয়া</span>
    </h3>
  </article>

  <!-- Card 3 -->
  <article class="flex flex-row items-start gap-3 border-b sm:border-b-0 border-gray-200 pb-4 sm:pb-0">
    <div class="w-3/5 aspect-video shrink-0 overflow-hidden rounded border-2 border-gray-400 bg-gray-100">
      <img 
        src="/images/topsideimage.png" 
        alt="Article 3" 
        class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" 
      />
    </div>
    <h3 class="w-2/5 font-bold text-sm md:text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
      <span>ডারউইন টেস্ট : ৫৭ রানের টার্গেট দিল অস্ট্রেলিয়া</span>
    </h3>
  </article>

  <!-- Card 4 -->
  <article class="flex flex-row items-start gap-3 border-b sm:border-b-0 border-gray-200 pb-4 sm:pb-0">
    <div class="w-3/5 aspect-video shrink-0 overflow-hidden rounded border-2 border-gray-400 bg-gray-100">
      <img 
        src="/images/topsideimage.png" 
        alt="Article 4" 
        class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300" 
      />
    </div>
    <h3 class="w-2/5 font-bold text-sm md:text-base leading-snug text-gray-900 hover:text-amber-700 cursor-pointer">
      <span>ডারউইন টেস্ট : ৫৭ রানের টার্গেট দিল অস্ট্রেলিয়া</span>
    </h3>
  </article>

</div>

    <!-- Top advert banner -->
    <div class="w-full flex items-center justify-center cursor-pointer p-4 border-b border-gray-200">
      <img src="/images/topbanner.png" alt="Top Banner" />
    </div>
  </div>
</template>

<script setup lang="ts">
</script>