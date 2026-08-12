<!-- layouts/DefaultLayout.vue -->

<template>

    <Navbar/>

    <main class="min-h-screen w-full overflow-x-hidden">
      <RouterView />
    </main>

</template>
<script setup lang="ts">
import Navbar from '@/views/NavbarView.vue'
</script>