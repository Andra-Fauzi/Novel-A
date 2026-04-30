<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Heart, Star, BookOpen, Download, ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    data: any;
    auth?: any;
    isLiked: boolean;
    isStarred: boolean;
}>();

const page = usePage();

// ── local reactive state (optimistic UI) ──────────────
const liked   = ref(props.isLiked);
const starred = ref(props.isStarred);
const localStars  = ref(props.data.data.stars ?? 0);
const localReaded = ref(props.data.data.readed ?? 0);

// ── Volume/Chapter pagination ─────────────────────────
const limit = 2;
const startNum = ref(0);

const visibleVolume = computed(() =>
    props.data.data.volumes.slice(startNum.value, startNum.value + limit)
);
const next = () => {
    if (startNum.value + limit < props.data.data.volumes.length) startNum.value += limit;
};
const previous = () => {
    if (startNum.value > 0) startNum.value -= limit;
};

// ── Comment pagination ────────────────────────────────
const limitComment = 5;
const startNumComment = ref(0);
const visibleComments = computed(() =>
    props.data.data.comments.slice(startNumComment.value, startNumComment.value + limitComment)
);
const nextComments = () => {
    if (startNumComment.value + limitComment < props.data.data.comments.length) startNumComment.value += limitComment;
};
const previousComments = () => {
    if (startNumComment.value > 0) startNumComment.value -= limitComment;
};

// ── Comment submit ────────────────────────────────────
const formComment = ref({
    comment: '',
    user_id: props.auth?.user?.id,
    novel_id: props.data.data.id,
});
const submitComment = () => {
    if (!props.auth?.user) return;
    router.post(route('comment'), formComment.value, {
        onSuccess: () => { formComment.value.comment = ''; },
    });
};

// ── Like toggle ───────────────────────────────────────
const toggleLike = () => {
    if (!page.props.auth?.user) { router.visit(route('login')); return; }
    liked.value = !liked.value;
    router.post(route('novel.like', props.data.data.id), {}, { preserveScroll: true });
};

// ── Star toggle ───────────────────────────────────────
const toggleStar = () => {
    if (!page.props.auth?.user) { router.visit(route('login')); return; }
    if (starred.value) {
        starred.value = false;
        localStars.value = Math.max(0, localStars.value - 1);
    } else {
        starred.value = true;
        localStars.value++;
    }
    router.post(route('novel.star', props.data.data.id), {}, { preserveScroll: true });
};

// ── Read counter (called once when Read link is clicked) ──
const getCsrfToken = (): string => {
    return (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
};

const onReadClick = () => {
    localReaded.value++;
    fetch(route('novel.read', props.data.data.id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
        },
    }).catch(() => {});
};

// ── Download counter ──────────────────────────────────
const localDownloaded = ref(props.data.data.downloaded ?? 0);

const onDownloadClick = () => {
    localDownloaded.value++;
    fetch(route('novel.download', props.data.data.id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
        },
    }).catch(() => {});
};
</script>

<template>
    <Navbar />
    <div class="min-h-screen bg-gray-950 text-white">
        <main class="max-w-5xl mx-auto px-4 py-8">

            <!-- Back -->
            <div class="mb-6">
                <Link :href="route('home')"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-white transition-colors">
                    <ChevronLeft class="w-4 h-4" />
                    Back to Home
                </Link>
            </div>

            <!-- Hero section -->
            <div class="flex flex-col md:flex-row gap-8 mb-8">
                <!-- Cover -->
                <div class="shrink-0 mx-auto md:mx-0">
                    <img
                        :src="'/storage/' + data.data.cover"
                        :alt="data.data.title"
                        class="w-44 h-64 md:w-52 md:h-72 object-cover rounded-2xl shadow-2xl shadow-black/60"
                    >
                </div>

                <!-- Info -->
                <div class="flex flex-col justify-between flex-1">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white leading-tight">{{ data.data.title }}</h1>
                        <p class="text-blue-400 font-semibold mt-1 text-lg">{{ data.data.author.author_name }}</p>

                        <!-- Status + badges -->
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                :class="{
                                    'bg-green-900/60 text-green-400': data.data.status === 'Ongoing',
                                    'bg-blue-900/60 text-blue-400': data.data.status === 'Completed',
                                    'bg-yellow-900/60 text-yellow-400': data.data.status === 'Hiatus',
                                }"
                            >{{ data.data.status }}</span>
                            <span v-for="genre in data.data.genres" :key="genre.id"
                                class="text-xs font-medium px-2.5 py-1 rounded-full bg-purple-900/40 text-purple-300">
                                {{ genre.name }}
                            </span>
                            <span v-for="cat in data.data.categories" :key="cat.id"
                                class="text-xs font-medium px-2.5 py-1 rounded-full bg-gray-700/60 text-gray-300">
                                {{ cat.name }}
                            </span>
                        </div>

                        <!-- Synopsis -->
                        <p class="text-gray-400 text-sm mt-4 leading-relaxed line-clamp-5">{{ data.data.description }}</p>
                    </div>

                    <!-- Stats row -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
                            <p class="text-xl font-bold text-white">{{ localReaded.toLocaleString() }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Reads</p>
                        </div>
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
                            <p class="text-xl font-bold text-white">{{ localStars }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Stars</p>
                        </div>
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
                            <p class="text-xl font-bold text-white">{{ localDownloaded.toLocaleString() }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Downloads</p>
                        </div>
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
                            <p class="text-xl font-bold text-white">{{ data.data.published_year }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Year</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-wrap gap-3 mb-8" v-if="data.data.volumes.length > 0">
                <!-- Read -->
                <Link
                    :href="route('content', data.data.id)"
                    :data="{ volume: data.data.volumes[0].no, chapter: data.data.volumes[0].chapters[0].no }"
                    @click="onReadClick"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-2.5 rounded-xl transition-all shadow-md shadow-blue-900/40"
                >
                    <BookOpen class="w-4 h-4" />
                    Start Reading
                </Link>

                <!-- Like -->
                <button
                    @click="toggleLike"
                    class="flex items-center gap-2 font-bold px-5 py-2.5 rounded-xl border-2 transition-all"
                    :class="liked
                        ? 'bg-red-600/20 border-red-500 text-red-400'
                        : 'bg-gray-900 border-gray-700 text-gray-400 hover:border-red-500 hover:text-red-400'"
                >
                    <Heart class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'" />
                    {{ liked ? 'Liked' : 'Like' }}
                </button>

                <!-- Star -->
                <button
                    @click="toggleStar"
                    class="flex items-center gap-2 font-bold px-5 py-2.5 rounded-xl border-2 transition-all"
                    :class="starred
                        ? 'bg-yellow-600/20 border-yellow-500 text-yellow-400'
                        : 'bg-gray-900 border-gray-700 text-gray-400 hover:border-yellow-500 hover:text-yellow-400'"
                >
                    <Star class="w-4 h-4" :fill="starred ? 'currentColor' : 'none'" />
                    {{ starred ? 'Starred' : 'Star' }}
                </button>

                <!-- Download -->
                <button
                    @click="onDownloadClick"
                    class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-300 font-bold px-5 py-2.5 rounded-xl transition-all"
                >
                    <Download class="w-4 h-4" />
                    Download
                </button>
            </div>

            <!-- Volumes & Chapters -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden mb-8">
                <div class="px-5 py-4 border-b border-gray-800">
                    <h2 class="font-bold text-white text-lg">📖 Volumes & Chapters</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ data.data.volumes.length }} volume(s) total</p>
                </div>

                <div v-if="data.data.volumes.length === 0" class="py-10 text-center text-gray-600">
                    No volumes yet.
                </div>

                <div v-for="volume in visibleVolume" :key="volume.id" class="border-b border-gray-800 last:border-b-0">
                    <div class="px-5 py-3 bg-gray-800/30">
                        <h3 class="font-semibold text-white text-sm">Volume {{ volume.no }}</h3>
                    </div>
                    <ul class="divide-y divide-gray-800/60">
                        <li v-for="chapter in volume.chapters" :key="chapter.id">
                            <Link
                                :href="route('content', data.data.id)"
                                :data="{ volume: volume.no, chapter: chapter.no }"
                                @click="onReadClick"
                                class="flex items-center gap-3 px-6 py-2.5 text-sm text-gray-400 hover:text-blue-400 hover:bg-gray-800/40 transition-colors"
                            >
                                <span class="w-6 text-gray-600 text-xs">{{ chapter.no }}</span>
                                Chapter {{ chapter.no }}
                            </Link>
                        </li>
                        <li v-if="!volume.chapters?.length" class="px-6 py-3 text-xs text-gray-600 italic">No chapters in this volume.</li>
                    </ul>
                </div>

                <!-- Pagination -->
                <div class="flex gap-2 px-5 py-3 border-t border-gray-800">
                    <button
                        v-if="startNum > 0"
                        @click="previous"
                        class="flex items-center gap-1 text-xs text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 px-3 py-1.5 rounded-lg transition-all"
                    >
                        <ChevronLeft class="w-3.5 h-3.5" /> Prev
                    </button>
                    <button
                        v-if="startNum + limit < data.data.volumes.length"
                        @click="next"
                        class="flex items-center gap-1 text-xs text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 px-3 py-1.5 rounded-lg transition-all"
                    >
                        Next <ChevronRight class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            <!-- Comments -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-800">
                    <h2 class="font-bold text-white text-lg">💬 Comments</h2>
                </div>

                <!-- Comment form -->
                <div class="p-5 border-b border-gray-800">
                    <template v-if="auth?.user">
                        <form @submit.prevent="submitComment" class="space-y-3">
                            <textarea
                                v-model="formComment.comment"
                                placeholder="Write a comment..."
                                class="w-full bg-gray-800 border border-gray-700 text-white px-4 py-3 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-500 resize-none min-h-[80px]"
                            />
                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2 rounded-lg text-sm transition-all"
                            >
                                Post Comment
                            </button>
                        </form>
                    </template>
                    <template v-else>
                        <p class="text-sm text-gray-500">
                            <Link :href="route('login')" class="text-blue-400 hover:underline">Log in</Link>
                            to leave a comment.
                        </p>
                    </template>
                </div>

                <!-- Comment list -->
                <div class="divide-y divide-gray-800/60">
                    <div v-for="comment in visibleComments" :key="comment.id" class="px-5 py-4">
                        <p class="text-sm font-semibold text-white">{{ comment.user.name }}</p>
                        <p class="text-sm text-gray-400 mt-1 whitespace-pre-line">{{ comment.comment }}</p>
                    </div>
                    <div v-if="!data.data.comments?.length" class="px-5 py-8 text-center text-gray-600 text-sm">
                        No comments yet. Be the first!
                    </div>
                </div>

                <!-- Comment pagination -->
                <div class="flex gap-2 px-5 py-3 border-t border-gray-800">
                    <button v-if="startNumComment > 0" @click="previousComments"
                        class="flex items-center gap-1 text-xs text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 px-3 py-1.5 rounded-lg transition-all">
                        <ChevronLeft class="w-3.5 h-3.5" /> Prev
                    </button>
                    <button v-if="startNumComment + limitComment < data.data.comments.length" @click="nextComments"
                        class="flex items-center gap-1 text-xs text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 px-3 py-1.5 rounded-lg transition-all">
                        Next <ChevronRight class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

        </main>

        <footer class="text-center py-6 text-xs text-gray-600 border-t border-gray-800 mt-8">
            &copy; 2026 Novel-A — Muhammad Andra Fauzi
        </footer>
    </div>
</template>