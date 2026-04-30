<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { ref } from 'vue';

const props = defineProps<{
    defaultMain?: string;
}>();

const main = ref(props.defaultMain ?? '');

const changeMain = (name: string) => {
    main.value = name;
};
</script>

<template>
    <div class="flex h-screen bg-slate-50 dark:bg-gray-950 text-slate-900 dark:text-white overflow-hidden transition-colors duration-300">
        <!-- Sidebar -->
        <aside class="flex flex-col w-64 min-h-screen bg-white dark:bg-gray-900 border-r border-slate-200 dark:border-gray-800 shrink-0">
            <!-- Logo area -->
            <div class="px-6 py-5 border-b border-slate-200 dark:border-gray-800">
                <span class="font-dancing-script text-3xl text-blue-600 dark:text-blue-400 font-bold tracking-wide">Novel-A</span>
            </div>

            <!-- Nav slots from parent -->
            <nav class="flex flex-col gap-1 p-3 flex-1 overflow-y-auto">
                <slot name="sidebar" :main="main" :changeMain="changeMain" />
            </nav>

            <!-- Bottom user area -->
            <div class="border-t border-slate-200 dark:border-gray-800 p-4 text-sm text-slate-500 dark:text-gray-400">
                <slot name="sidebar-footer" />
            </div>
        </aside>

        <!-- Main content area -->
        <div class="flex flex-col flex-1 min-h-screen overflow-hidden">
            <!-- Top navbar -->
            <Navbar />

            <!-- Page header -->
            <header class="px-6 py-4 border-b border-slate-200 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm">
                <slot name="header" :main="main" />
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto bg-slate-50 dark:bg-gray-950 p-0">
                <slot :main="main" :changeMain="changeMain" />
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-200 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50 text-center text-xs text-slate-500 dark:text-gray-500 py-3">
                <slot name="footer">
                    &copy; 2026 Novel-A — Muhammad Andra Fauzi
                </slot>
            </footer>
        </div>
    </div>
</template>