<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Flame, Star, TrendingUp, Sparkles, BookOpen, ChevronRight, ChevronLeft, ChevronDown } from 'lucide-vue-next';

const props = defineProps<{
    datas: any,
    recommends: any,
    stars: any,
    newbooks: any,
    topreaders: any,
    carousels: any[],
    auth: any
}>()

// ── Carousel ────────────────────────────────────
const carousel = ref<HTMLElement | null>(null)
const currentIndex = ref(0)
let carouselInterval: any = null;

const total = computed(() => props.carousels?.length || 0)

const next = () => {
    if (total.value === 0) return;
    currentIndex.value = currentIndex.value >= total.value - 1 ? 0 : currentIndex.value + 1;
    scrollToCurrent();
}

const prev = () => {
    if (total.value === 0) return;
    currentIndex.value = currentIndex.value <= 0 ? total.value - 1 : currentIndex.value - 1;
    scrollToCurrent();
}

const scrollToCurrent = () => {
    if (carousel.value) {
        carousel.value.scrollTo({
            left: carousel.value.clientWidth * currentIndex.value,
            behavior: 'smooth'
        })
    }
}

onMounted(() => {
    carouselInterval = setInterval(next, 5000);
});

onUnmounted(() => {
    if (carouselInterval) clearInterval(carouselInterval);
});

// ── Books Pagination ─────────────────────────────
const visibleCount = ref(20)

const visibledata = computed(() => {
    return props.datas?.datas?.slice(0, visibleCount.value) || []
})

const loadMore = () => {
    visibleCount.value += 20;
}
</script>

<style>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-950 text-slate-900 dark:text-gray-200 font-sans selection:bg-blue-500/30 transition-colors duration-300">
        <!-- Navbar -->
        <Navbar />

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-16">
            
            <!-- ── Carousel ── -->
            <section class="relative rounded-3xl overflow-hidden shadow-2xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-200 dark:border-gray-800 group">
                <div ref="carousel" class="flex w-full overflow-hidden scroll-smooth snap-x snap-mandatory">
                    <template v-if="carousels?.length > 0">
                        <div v-for="c in carousels" :key="c.id" class="w-full shrink-0 h-[250px] sm:h-[350px] md:h-[450px] lg:h-[500px] relative snap-center">
                            <component :is="c.link ? Link : 'div'" :href="c.link || '#'" class="block w-full h-full">
                                <img :src="'/storage/' + c.image" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent dark:from-gray-950 dark:via-gray-950/20 to-transparent pointer-events-none"></div>
                            </component>
                        </div>
                    </template>
                    <template v-else>
                        <div class="w-full shrink-0 h-[300px] md:h-[450px] bg-slate-200 dark:bg-gray-900 flex items-center justify-center text-slate-500 dark:text-gray-500 font-medium">
                            <span class="flex items-center gap-2"><Sparkles class="w-5 h-5"/> No Carousel Banner Available</span>
                        </div>
                    </template>
                </div>

                <!-- Carousel Controls -->
                <button v-if="carousels?.length > 1" @click="prev" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/40 hover:bg-black/70 backdrop-blur-sm text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all border border-white/10">
                    <ChevronLeft class="w-6 h-6" />
                </button>
                <button v-if="carousels?.length > 1" @click="next" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/40 hover:bg-black/70 backdrop-blur-sm text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all border border-white/10">
                    <ChevronRight class="w-6 h-6" />
                </button>

                <!-- Carousel Indicators -->
                <div v-if="carousels?.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2">
                    <button v-for="(_, index) in carousels" :key="index" @click="currentIndex = index; scrollToCurrent()"
                        class="w-2.5 h-2.5 rounded-full transition-all"
                        :class="currentIndex === index ? 'bg-blue-500 w-6' : 'bg-white/50 hover:bg-white/80'"
                    ></button>
                </div>
            </section>

            <!-- ── Editor's Recommendation ── -->
            <section v-if="recommends?.recommends?.length">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-rose-100 dark:bg-rose-500/20 rounded-xl text-rose-500">
                        <Flame class="w-6 h-6" />
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Editor's Picks</h2>
                </div>
                <div class="flex gap-6 overflow-x-auto no-scrollbar pb-4 -mx-4 px-4 sm:mx-0 sm:px-0">
                    <Link v-for="novel in recommends.recommends" :key="novel.id" :href="route('detail', novel.slug)"
                        class="w-[140px] md:w-[180px] lg:w-[200px] shrink-0 group"
                    >
                        <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-slate-200 dark:bg-gray-800 border border-slate-200 dark:border-gray-800 group-hover:border-rose-500/50 group-hover:shadow-lg group-hover:shadow-rose-500/20 dark:group-hover:shadow-rose-900/20 transition-all">
                            <img :src="'/storage/' + novel.cover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 dark:from-gray-950 via-transparent to-transparent opacity-80"></div>
                        </div>
                        <h3 class="mt-3 text-sm md:text-base font-bold text-slate-800 dark:text-gray-200 group-hover:text-rose-500 dark:group-hover:text-rose-400 transition-colors line-clamp-2 leading-snug">
                            {{ novel.title }}
                        </h3>
                    </Link>
                </div>
            </section>

            <!-- ── New Fresh Books ── -->
            <section v-if="newbooks?.newbooks?.length">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl text-emerald-500">
                        <Sparkles class="w-6 h-6" />
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">New Fresh Books</h2>
                </div>
                <div class="flex gap-6 overflow-x-auto no-scrollbar pb-4 -mx-4 px-4 sm:mx-0 sm:px-0">
                    <Link v-for="novel in newbooks.newbooks" :key="novel.id" :href="route('detail', novel.slug)"
                        class="w-[140px] md:w-[180px] lg:w-[200px] shrink-0 group"
                    >
                        <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-slate-200 dark:bg-gray-800 border border-slate-200 dark:border-gray-800 group-hover:border-emerald-500/50 group-hover:shadow-lg group-hover:shadow-emerald-500/20 dark:group-hover:shadow-emerald-900/20 transition-all">
                            <img :src="'/storage/' + novel.cover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 dark:from-gray-950 via-transparent to-transparent opacity-80"></div>
                        </div>
                        <h3 class="mt-3 text-sm md:text-base font-bold text-slate-800 dark:text-gray-200 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                            {{ novel.title }}
                        </h3>
                    </Link>
                </div>
            </section>

            <!-- ── Top Readers ── -->
            <section v-if="topreaders?.topreaders?.length">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-blue-100 dark:bg-blue-500/20 rounded-xl text-blue-500">
                        <TrendingUp class="w-6 h-6" />
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Most Read Novels</h2>
                </div>
                <div class="flex gap-6 overflow-x-auto no-scrollbar pb-4 -mx-4 px-4 sm:mx-0 sm:px-0">
                    <Link v-for="novel in topreaders.topreaders" :key="novel.id" :href="route('detail', novel.slug)"
                        class="w-[140px] md:w-[180px] lg:w-[200px] shrink-0 group"
                    >
                        <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-slate-200 dark:bg-gray-800 border border-slate-200 dark:border-gray-800 group-hover:border-blue-500/50 group-hover:shadow-lg group-hover:shadow-blue-500/20 dark:group-hover:shadow-blue-900/20 transition-all">
                            <img :src="'/storage/' + novel.cover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 dark:from-gray-950 via-transparent to-transparent opacity-80"></div>
                            
                            <div class="absolute top-2 right-2 bg-blue-600/90 backdrop-blur text-white text-xs font-bold px-2.5 py-1 rounded-md shadow-md flex items-center gap-1.5">
                                <BookOpen class="w-3 h-3" /> {{ novel.readed || 0 }}
                            </div>
                        </div>
                        <h3 class="mt-3 text-sm md:text-base font-bold text-slate-800 dark:text-gray-200 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors line-clamp-2 leading-snug">
                            {{ novel.title }}
                        </h3>
                    </Link>
                </div>
            </section>

            <!-- ── Top Stars ── -->
            <section v-if="stars?.stars?.length">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-500/20 rounded-xl text-amber-500">
                        <Star class="w-6 h-6" />
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Highest Rated</h2>
                </div>
                <div class="flex gap-6 overflow-x-auto no-scrollbar pb-4 -mx-4 px-4 sm:mx-0 sm:px-0">
                    <Link v-for="novel in stars.stars" :key="novel.id" :href="route('detail', novel.slug)"
                        class="w-[140px] md:w-[180px] lg:w-[200px] shrink-0 group"
                    >
                        <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-slate-200 dark:bg-gray-800 border border-slate-200 dark:border-gray-800 group-hover:border-amber-500/50 group-hover:shadow-lg group-hover:shadow-amber-500/20 dark:group-hover:shadow-amber-900/20 transition-all">
                            <img :src="'/storage/' + novel.cover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 dark:from-gray-950 via-transparent to-transparent opacity-80"></div>
                            
                            <div class="absolute top-2 right-2 bg-amber-500/90 backdrop-blur text-white text-xs font-bold px-2.5 py-1 rounded-md shadow-md flex items-center gap-1.5">
                                <Star class="w-3 h-3 fill-current" /> {{ novel.stars || 0 }}
                            </div>
                        </div>
                        <h3 class="mt-3 text-sm md:text-base font-bold text-slate-800 dark:text-gray-200 group-hover:text-amber-500 dark:group-hover:text-amber-400 transition-colors line-clamp-2 leading-snug">
                            {{ novel.title }}
                        </h3>
                    </Link>
                </div>
            </section>

            <!-- ── Discover More Grid ── -->
            <section class="pt-12 border-t border-slate-200 dark:border-gray-800/60">
                <div class="text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Discover More</h2>
                    <p class="text-slate-500 mt-2">Find your next favorite story from our extensive library</p>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                    <Link v-for="data in visibledata" :key="data.id" :href="route('detail', data.slug)"
                        class="group"
                    >
                        <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-slate-200 dark:bg-gray-900 border border-slate-200 dark:border-gray-800 group-hover:border-blue-500/50 transition-all shadow-md group-hover:shadow-blue-500/10 dark:group-hover:shadow-blue-900/10">
                            <img :src="'/storage/' + data.cover" class="w-full h-full object-cover group-hover:opacity-80 transition-opacity" />
                        </div>
                        <h3 class="mt-3 text-sm md:text-base font-bold text-slate-800 dark:text-gray-300 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors line-clamp-2 leading-snug">
                            {{ data.title }}
                        </h3>
                    </Link>
                </div>
                
                <div class="mt-12 flex justify-center" v-if="visibleCount < props.datas?.datas?.length">
                    <button @click="loadMore" class="bg-white dark:bg-gray-900 hover:bg-slate-50 dark:hover:bg-gray-800 text-slate-700 dark:text-gray-300 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-gray-700 hover:border-blue-500 font-semibold px-8 py-3.5 rounded-xl transition-all shadow-sm flex items-center gap-2">
                        Load More Novels <ChevronDown class="w-4 h-4" />
                    </button>
                </div>
            </section>

        </main>

        <footer class="mt-20 py-8 border-t border-slate-200 dark:border-gray-900 bg-white/40 dark:bg-black/40 text-center">
            <p class="text-slate-500 dark:text-gray-500 text-sm font-medium">
                &copy; {{ new Date().getFullYear() }} Novel-A. Built by Muhammad Andra Fauzi.
            </p>
        </footer>
    </div>
</template>