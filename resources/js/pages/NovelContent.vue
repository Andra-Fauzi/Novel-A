<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, BookOpen } from 'lucide-vue-next';

const props = defineProps<{
    data: any
}>();

const novel = props.data.data;
const volume = novel.volumes?.[0];
const chapter = volume?.chapters?.[0];
const contentText = chapter?.content?.content || 'No content available for this chapter.';
</script>

<template>
    <div class="min-h-screen bg-gray-950 text-white font-sans selection:bg-blue-500/30">
        <Navbar />

        <!-- Top Navigation Bar (Sticky) -->
        <div class="sticky top-0 z-10 bg-gray-950/80 backdrop-blur-md border-b border-gray-800">
            <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
                <Link :href="route('detail', novel.id)" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors">
                    <ChevronLeft class="w-4 h-4" />
                    Back to Detail
                </Link>
                <div class="text-sm font-semibold text-gray-300 truncate max-w-[200px] md:max-w-md text-center hidden md:block">
                    {{ novel.title }}
                </div>
                <div class="w-24"></div> <!-- Spacer for flex centering -->
            </div>
        </div>

        <!-- Main Reading Area -->
        <main class="max-w-3xl mx-auto px-6 py-10 md:py-16">
            <!-- Header -->
            <header class="text-center mb-12 space-y-4">
                <h2 class="text-lg md:text-xl text-blue-400 font-semibold tracking-wide uppercase">
                    Volume {{ volume?.no }}
                </h2>
                <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight">
                    Chapter {{ chapter?.no }}
                </h1>
                
                <div class="flex items-center justify-center gap-3 mt-8">
                    <span class="h-px w-16 bg-gray-800"></span>
                    <BookOpen class="w-5 h-5 text-gray-500" />
                    <span class="h-px w-16 bg-gray-800"></span>
                </div>
            </header>

            <!-- Content -->
            <article class="prose prose-invert prose-lg max-w-none prose-p:leading-relaxed prose-p:mb-6 text-gray-300 whitespace-pre-line text-lg md:text-xl font-medium tracking-wide">
                {{ contentText }}
            </article>

            <!-- Bottom Navigation -->
            <div class="mt-20 pt-8 border-t border-gray-800 flex justify-center">
                <Link :href="route('detail', novel.id)" class="inline-flex items-center gap-2 px-8 py-3.5 bg-gray-900 hover:bg-gray-800 border border-gray-700 rounded-xl text-gray-300 hover:text-white transition-all font-semibold shadow-lg">
                    <ChevronLeft class="w-5 h-5" />
                    Return to Novel Details
                </Link>
            </div>
        </main>

        <footer class="text-center py-8 text-xs text-gray-600 border-t border-gray-800 mt-8">
            &copy; 2026 Novel-A — Muhammad Andra Fauzi
        </footer>
    </div>
</template>