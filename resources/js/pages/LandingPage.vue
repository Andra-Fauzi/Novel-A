<script setup lang="ts">
    import Navbar from "@/components/Navbar.vue"
    import { Link } from "@inertiajs/vue3";
    let user: number = 0;
</script>

<template>
    <Navbar />
    <div class="flex flex-col items-center justify-center min-h-screen">
        <h1 class="text-4xl font-bold mb-4">Welcome to <span class="font-dancing-script">Novel-A</span></h1>
        <p class="text-lg text-gray-600">Give a Life to Novel, Not Life to Novel</p>
        <Link :href="route('home')" :class="'bg-blue-500 rounded text-white hover:bg-blue-700 font-bold py-2 px-4 mt-4 '">Get Started</Link>
    </div>
    <footer class="text-center my-5">
        &copy; 2026 Website by Muhammad Andra Fauzi 
    </footer>
</template>