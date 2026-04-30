<script setup lang="ts">
    import Navbar from "@/components/Navbar.vue"
    import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen flex flex-col font-sans bg-slate-50 dark:bg-gray-950 text-slate-900 dark:text-gray-200 selection:bg-blue-500/30 transition-colors duration-300">
        <Navbar />
        
        <main class="flex-grow flex flex-col items-center justify-center relative overflow-hidden">
            <!-- Background Decorations -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob pointer-events-none"></div>
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob animation-delay-2000 pointer-events-none"></div>
            <div class="absolute -bottom-8 left-1/2 w-96 h-96 bg-indigo-600/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob animation-delay-4000 pointer-events-none"></div>

            <div class="z-10 text-center px-4 max-w-4xl mx-auto flex flex-col items-center">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm font-medium mb-8 animate-fade-in-up">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.8)]"></span>
                    Discover the world of infinite stories
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 animate-fade-in-up text-slate-900 dark:text-white" style="animation-delay: 0.1s;">
                    Welcome to <br class="hidden md:block" />
                    <span class="font-dancing-script text-6xl md:text-8xl font-normal bg-clip-text text-transparent bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400 pb-2 inline-block">Novel-A</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-slate-600 dark:text-gray-400 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s;">
                    Give Life to the Novel, <br class="md:hidden"/> Not Life to the Novel. <br />
                    <span class="text-lg md:text-xl text-slate-500 dark:text-gray-500 mt-3 block font-light">Immerse yourself in a universe where imagination meets reality.</span>
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <Link :href="route('home')" class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-bold text-lg shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                        <span class="relative flex items-center justify-center gap-2">
                            Get Started
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </Link>
                </div>
            </div>
        </main>

        <footer class="py-8 text-center text-slate-500 dark:text-gray-500 text-sm border-t border-slate-200 dark:border-gray-800/50 backdrop-blur-md z-10 relative">
            <p>&copy; {{ new Date().getFullYear() }} Website by <span class="font-medium text-slate-700 dark:text-gray-400">Muhammad Andra Fauzi</span>. All rights reserved.</p>
        </footer>
    </div>
</template>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 10s infinite alternate;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
.animation-delay-4000 {
  animation-delay: 4s;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
}
</style>