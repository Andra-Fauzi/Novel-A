<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import SidebarDashboardButton from '@/components/SidebarDashboardButton.vue';
import { Heart, Star, BookOpen, User } from 'lucide-vue-next';

const props = defineProps<{
    likedNovels: any[];
    starredNovels: any[];
}>();

const page = usePage();
const currentView = ref('Liked Novels');

const user = (page.props as any).auth?.user;
</script>

<template>
    <DashboardLayout>
        <!-- ── SIDEBAR ───────────────────────── -->
        <template #sidebar>
            <div class="mb-4 px-2">
                <p class="text-xs text-slate-500 dark:text-gray-500 uppercase tracking-widest font-semibold mb-2 px-2">My Library</p>
                <SidebarDashboardButton
                    :main="currentView"
                    :change-main="(n: string) => currentView = n"
                    button-name="Liked Novels"
                    :icon="Heart"
                />
                <SidebarDashboardButton
                    :main="currentView"
                    :change-main="(n: string) => currentView = n"
                    button-name="Starred Novels"
                    :icon="Star"
                />
            </div>

            <!-- User info -->
            <div class="mt-auto px-4 py-3 bg-slate-100 dark:bg-gray-800/50 rounded-lg mx-2">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0">
                        <User class="w-4 h-4 text-white" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ user?.name ?? 'User' }}</p>
                        <p class="text-xs text-slate-500 dark:text-gray-500 truncate">{{ user?.email ?? '' }}</p>
                    </div>
                </div>
                <div class="flex gap-3 mt-2 text-xs text-slate-500 dark:text-gray-500">
                    <span>❤️ {{ likedNovels.length }} liked</span>
                    <span>⭐ {{ starredNovels.length }} starred</span>
                </div>
            </div>
        </template>

        <!-- ── HEADER ────────────────────────── -->
        <template #header>
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">
                    <span v-if="currentView === 'Liked Novels'">❤️ Liked Novels</span>
                    <span v-else>⭐ Starred Novels</span>
                </h1>
                <p class="text-slate-500 dark:text-gray-400 text-sm mt-0.5">
                    <span v-if="currentView === 'Liked Novels'">
                        {{ likedNovels.length }} novel(s) you've liked
                    </span>
                    <span v-else>
                        {{ starredNovels.length }} novel(s) you've starred
                    </span>
                </p>
            </div>
        </template>

        <!-- ── MAIN ──────────────────────────── -->
        <template #default>

            <!-- ─ LIKED NOVELS ─ -->
            <div v-if="currentView === 'Liked Novels'" class="p-6">
                <!-- Empty state -->
                <div v-if="!likedNovels.length"
                    class="flex flex-col items-center justify-center py-24 text-center">
                    <Heart class="w-16 h-16 text-slate-300 dark:text-gray-700 mb-4" />
                    <h2 class="text-xl font-semibold text-slate-500 dark:text-gray-400">No liked novels</h2>
                    <p class="text-slate-600 dark:text-gray-600 text-sm mt-1">Go browse novels and hit the ❤️ Like button!</p>
                    <Link :href="route('home')"
                        class="mt-5 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all">
                        <BookOpen class="w-4 h-4" />
                        Browse Novels
                    </Link>
                </div>

                <!-- Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <Link
                        v-for="novel in likedNovels"
                        :key="novel.id"
                        :href="route('detail', novel.slug)"
                        class="group bg-white dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-2xl overflow-hidden hover:border-red-500/50 hover:shadow-lg hover:shadow-red-500/10 dark:hover:shadow-red-900/10 transition-all duration-200"
                    >
                        <div class="relative">
                            <img
                                :src="novel.cover ? '/storage/' + novel.cover : 'https://placehold.co/400x560/1f2937/6b7280?text=No+Cover'"
                                :alt="novel.title"
                                class="w-full aspect-[2/3] object-cover group-hover:opacity-90 transition-opacity"
                            >
                            <!-- Liked badge -->
                            <div class="absolute top-2 right-2 bg-red-600/80 backdrop-blur-sm rounded-full p-1.5">
                                <Heart class="w-3.5 h-3.5 text-white fill-white" />
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="text-slate-900 dark:text-white font-semibold text-sm line-clamp-2 leading-snug">{{ novel.title }}</h3>
                            <p class="text-slate-500 dark:text-gray-500 text-xs mt-1">{{ novel.author?.author_name ?? '—' }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900/60 dark:text-green-400': novel.status === 'Ongoing',
                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-400': novel.status === 'Completed',
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/60 dark:text-yellow-400': novel.status === 'Hiatus',
                                    }"
                                >{{ novel.status ?? 'Ongoing' }}</span>
                                <span class="text-xs text-slate-500 dark:text-gray-500">⭐ {{ novel.stars ?? 0 }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- ─ STARRED NOVELS ─ -->
            <div v-else-if="currentView === 'Starred Novels'" class="p-6">
                <!-- Empty state -->
                <div v-if="!starredNovels.length"
                    class="flex flex-col items-center justify-center py-24 text-center">
                    <Star class="w-16 h-16 text-slate-300 dark:text-gray-700 mb-4" />
                    <h2 class="text-xl font-semibold text-slate-500 dark:text-gray-400">No starred novels</h2>
                    <p class="text-slate-600 dark:text-gray-600 text-sm mt-1">Star novels you want to rate and follow!</p>
                    <Link :href="route('home')"
                        class="mt-5 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all">
                        <BookOpen class="w-4 h-4" />
                        Browse Novels
                    </Link>
                </div>

                <!-- Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <Link
                        v-for="novel in starredNovels"
                        :key="novel.id"
                        :href="route('detail', novel.slug)"
                        class="group bg-white dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-2xl overflow-hidden hover:border-yellow-500/50 hover:shadow-lg hover:shadow-yellow-500/10 dark:hover:shadow-yellow-900/10 transition-all duration-200"
                    >
                        <div class="relative">
                            <img
                                :src="novel.cover ? '/storage/' + novel.cover : 'https://placehold.co/400x560/1f2937/6b7280?text=No+Cover'"
                                :alt="novel.title"
                                class="w-full aspect-[2/3] object-cover group-hover:opacity-90 transition-opacity"
                            >
                            <!-- Starred badge -->
                            <div class="absolute top-2 right-2 bg-yellow-600/80 backdrop-blur-sm rounded-full p-1.5">
                                <Star class="w-3.5 h-3.5 text-white fill-white" />
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="text-slate-900 dark:text-white font-semibold text-sm line-clamp-2 leading-snug">{{ novel.title }}</h3>
                            <p class="text-slate-500 dark:text-gray-500 text-xs mt-1">{{ novel.author?.author_name ?? '—' }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900/60 dark:text-green-400': novel.status === 'Ongoing',
                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-400': novel.status === 'Completed',
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/60 dark:text-yellow-400': novel.status === 'Hiatus',
                                    }"
                                >{{ novel.status ?? 'Ongoing' }}</span>
                                <span class="text-xs text-slate-500 dark:text-gray-500">⭐ {{ novel.stars ?? 0 }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

        </template>
    </DashboardLayout>
</template>