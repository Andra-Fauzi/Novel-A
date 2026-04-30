<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import SidebarDashboardButton from '@/components/SidebarDashboardButton.vue';
import { BookMarked, ChevronDown, ChevronRight, Plus, X, ArrowLeft, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    novel: any;
}>();

// ── Accordion ──────────────────────────────────
const openVolumes = ref<Set<number>>(new Set());

const toggleVolume = (volId: number) => {
    if (openVolumes.value.has(volId)) {
        openVolumes.value.delete(volId);
    } else {
        openVolumes.value.add(volId);
    }
};

// ── Add Volume ──────────────────────────────────
const showAddVolume = ref(false);
const volumeForm = useForm({ no: '' });

const submitVolume = () => {
    volumeForm.post(route('author.volume.store', props.novel.id), {
        onSuccess: () => {
            showAddVolume.value = false;
            volumeForm.reset();
        },
    });
};

// ── Edit Volume ─────────────────────────────────
const showEditVolume = ref(false);
const editingVolumeId = ref<number | null>(null);
const volumeEditForm = useForm({ no: '' });

const openEditVolume = (vol: any) => {
    editingVolumeId.value = vol.id;
    volumeEditForm.no = vol.no;
    showEditVolume.value = true;
};

const submitEditVolume = () => {
    if (editingVolumeId.value) {
        volumeEditForm.patch(route('author.volume.update', editingVolumeId.value), {
            onSuccess: () => {
                showEditVolume.value = false;
                editingVolumeId.value = null;
                volumeEditForm.reset();
            },
        });
    }
};

// ── Delete Volume ───────────────────────────────
const deleteVolume = (id: number) => {
    if (confirm('Are you sure you want to delete this volume and all its chapters?')) {
        router.delete(route('author.volume.delete', id));
    }
};

// ── Add Chapter ─────────────────────────────────
const addingChapterFor = ref<number | null>(null);
const chapterForm = useForm({
    no: '',
    content: '',
    language: 'id',
});

const openAddChapter = (volumeId: number) => {
    addingChapterFor.value = volumeId;
    editingChapterFor.value = null; // Close any edit form
    chapterForm.reset();
};

const closeAddChapter = () => {
    addingChapterFor.value = null;
    chapterForm.reset();
};

const submitChapter = (volumeId: number) => {
    chapterForm.post(route('author.chapter.store', volumeId), {
        onSuccess: () => {
            closeAddChapter();
        },
    });
};

// ── Edit Chapter ────────────────────────────────
const editingChapterFor = ref<number | null>(null);
const chapterEditForm = useForm({
    no: '',
    content: '',
    language: 'id',
});

const openEditChapter = (ch: any) => {
    editingChapterFor.value = ch.id;
    addingChapterFor.value = null; // Close any add form
    chapterEditForm.no = ch.no;
    chapterEditForm.content = ch.content?.content || '';
    chapterEditForm.language = ch.content?.language || 'id';
};

const closeEditChapter = () => {
    editingChapterFor.value = null;
    chapterEditForm.reset();
};

const submitEditChapter = (chapterId: number) => {
    chapterEditForm.patch(route('author.chapter.update', chapterId), {
        onSuccess: () => {
            closeEditChapter();
        },
    });
};

// ── Delete Chapter ──────────────────────────────
const deleteChapter = (id: number) => {
    if (confirm('Are you sure you want to delete this chapter?')) {
        router.delete(route('author.chapter.delete', id));
    }
};

// ── Next vol/chapter number suggestion ──────────
const nextVolumeNo = () => {
    const nos = props.novel.volumes?.map((v: any) => v.no) ?? [];
    return nos.length ? Math.max(...nos) + 1 : 1;
};

const nextChapterNo = (volumeId: number) => {
    const vol = props.novel.volumes?.find((v: any) => v.id === volumeId);
    const nos = vol?.chapters?.map((c: any) => c.no) ?? [];
    return nos.length ? Math.max(...nos) + 1 : 1;
};
</script>

<template>
    <DashboardLayout>
        <!-- ── SIDEBAR ───────────────────────── -->
        <template #sidebar>
            <div class="mb-4 px-2">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-2 px-2">Author Panel</p>
                <Link
                    :href="route('author.dashboard')"
                    class="flex items-center gap-3 w-full px-4 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-all"
                >
                    <ArrowLeft class="w-4 h-4" />
                    Back to Dashboard
                </Link>
            </div>

            <!-- Novel info in sidebar -->
            <div class="mx-2 mt-2 bg-gray-800/60 rounded-xl p-3 space-y-2">
                <img
                    :src="novel.cover ? '/storage/' + novel.cover : 'https://placehold.co/200x280/1f2937/6b7280?text=No+Cover'"
                    :alt="novel.title"
                    class="w-full aspect-[2/3] object-cover rounded-lg shadow"
                >
                <p class="text-white font-bold text-sm line-clamp-2">{{ novel.title }}</p>
                <p class="text-gray-500 text-xs">
                    {{ novel.volumes?.length ?? 0 }} volume(s) ·
                    {{ novel.volumes?.reduce((sum: number, v: any) => sum + (v.chapters?.length ?? 0), 0) ?? 0 }} chapter(s)
                </p>
            </div>
        </template>

        <!-- ── HEADER ────────────────────────── -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white">📖 Manage Volumes & Chapters</h1>
                    <p class="text-gray-400 text-sm mt-0.5">{{ novel.title }}</p>
                </div>
                <button
                    @click="showAddVolume = true"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-all shadow-md shadow-blue-900/30"
                >
                    <Plus class="w-4 h-4" />
                    Add Volume
                </button>
            </div>
        </template>

        <!-- ── MAIN ──────────────────────────── -->
        <template #default>
            <div class="p-6 space-y-4">

                <!-- Empty state -->
                <div v-if="!novel.volumes?.length"
                    class="flex flex-col items-center justify-center py-24 text-center">
                    <BookMarked class="w-16 h-16 text-gray-700 mb-4" />
                    <h2 class="text-xl font-semibold text-gray-400">No volumes yet</h2>
                    <p class="text-gray-600 text-sm mt-1">Add the first volume to start building this novel.</p>
                    <button
                        @click="showAddVolume = true"
                        class="mt-5 flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all"
                    >
                        <Plus class="w-4 h-4" />
                        Add Volume
                    </button>
                </div>

                <!-- Volume accordion list -->
                <div v-for="vol in novel.volumes" :key="vol.id"
                    class="bg-gray-900 border border-gray-700 rounded-2xl overflow-hidden">

                    <!-- Volume header -->
                    <div
                        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-800/60 transition-colors group cursor-pointer"
                        @click="toggleVolume(vol.id)"
                    >
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-600/20 text-blue-400 text-xs font-bold px-2.5 py-1 rounded-lg">
                                Vol {{ vol.no }}
                            </span>
                            <span class="text-white font-semibold">Volume {{ vol.no }}</span>
                            <span class="text-gray-500 text-xs">{{ vol.chapters?.length ?? 0 }} chapter(s)</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <!-- Volume Actions -->
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-2" @click.stop>
                                <button @click="openEditVolume(vol)" class="text-gray-400 hover:text-blue-400 p-1 rounded hover:bg-gray-800 transition-colors" title="Edit Volume">
                                    <Pencil class="w-4 h-4" />
                                </button>
                                <button @click="deleteVolume(vol.id)" class="text-gray-400 hover:text-red-400 p-1 rounded hover:bg-gray-800 transition-colors" title="Delete Volume">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                            
                            <ChevronDown v-if="openVolumes.has(vol.id)" class="w-4 h-4 text-gray-400" />
                            <ChevronRight v-else class="w-4 h-4 text-gray-400" />
                        </div>
                    </div>

                    <!-- Chapters list (collapsed) -->
                    <div v-if="openVolumes.has(vol.id)" class="border-t border-gray-800">
                        <!-- Chapter rows -->
                        <div v-for="ch in vol.chapters" :key="ch.id" class="border-b border-gray-800/60">
                            
                            <!-- Chapter view -->
                            <div v-if="editingChapterFor !== ch.id"
                                class="flex items-center justify-between px-6 py-3 hover:bg-gray-800/30 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-500 text-xs w-16">Chapter {{ ch.no }}</span>
                                    <span class="text-gray-400 text-xs">
                                        {{ ch.content?.total_characters ?? 0 }} chars
                                    </span>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-3">
                                    <button @click="openEditChapter(ch)" class="text-gray-500 hover:text-blue-400 p-1 rounded hover:bg-gray-800 transition-colors" title="Edit Chapter">
                                        <Pencil class="w-3.5 h-3.5" />
                                    </button>
                                    <button @click="deleteChapter(ch.id)" class="text-gray-500 hover:text-red-400 p-1 rounded hover:bg-gray-800 transition-colors" title="Delete Chapter">
                                        <Trash2 class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Edit Chapter Form -->
                            <div v-if="editingChapterFor === ch.id"
                                class="p-5 bg-gray-800/60 border-t border-gray-700 border-b border-gray-700 space-y-3">
                                <h3 class="text-sm font-semibold text-gray-300">Edit Chapter {{ ch.no }}</h3>

                                <form @submit.prevent="submitEditChapter(ch.id)" class="space-y-3">
                                    <div class="flex gap-3">
                                        <div class="flex flex-col gap-1 w-32">
                                            <label class="text-xs text-gray-400">Chapter No.</label>
                                            <input
                                                v-model="chapterEditForm.no"
                                                type="number"
                                                class="bg-gray-900 border border-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                                                required
                                            >
                                        </div>
                                        <div class="flex flex-col gap-1 w-28">
                                            <label class="text-xs text-gray-400">Language</label>
                                            <select
                                                v-model="chapterEditForm.language"
                                                class="bg-gray-900 border border-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                                            >
                                                <option value="id">Indonesian</option>
                                                <option value="en">English</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs text-gray-400">Chapter Content</label>
                                        <textarea
                                            v-model="chapterEditForm.content"
                                            class="bg-gray-900 border border-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none min-h-[200px] resize-y"
                                            required
                                        />
                                    </div>

                                    <div class="flex gap-2">
                                        <button
                                            type="submit"
                                            :disabled="chapterEditForm.processing"
                                            class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2 rounded-lg text-sm transition-all disabled:bg-gray-600 disabled:cursor-not-allowed"
                                        >
                                            {{ chapterEditForm.processing ? 'Saving...' : '✓ Update Chapter' }}
                                        </button>
                                        <button
                                            type="button"
                                            @click="closeEditChapter"
                                            class="bg-gray-700 hover:bg-gray-600 text-white font-semibold px-5 py-2 rounded-lg text-sm transition-all"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>

                        <!-- Empty chapters -->
                        <div v-if="!vol.chapters?.length"
                            class="px-6 py-4 text-gray-600 text-sm text-center italic">
                            No chapters yet in this volume.
                        </div>

                        <!-- Add Chapter form -->
                        <div v-if="addingChapterFor === vol.id"
                            class="p-5 bg-gray-800/40 border-t border-gray-700 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-300">Add Chapter to Volume {{ vol.no }}</h3>

                            <form @submit.prevent="submitChapter(vol.id)" class="space-y-3">
                                <div class="flex gap-3">
                                    <div class="flex flex-col gap-1 w-32">
                                        <label class="text-xs text-gray-400">Chapter No.</label>
                                        <input
                                            v-model="chapterForm.no"
                                            type="number"
                                            :placeholder="String(nextChapterNo(vol.id))"
                                            class="bg-gray-900 border border-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                                            required
                                        >
                                    </div>
                                    <div class="flex flex-col gap-1 w-28">
                                        <label class="text-xs text-gray-400">Language</label>
                                        <select
                                            v-model="chapterForm.language"
                                            class="bg-gray-900 border border-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                                        >
                                            <option value="id">Indonesian</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label class="text-xs text-gray-400">Chapter Content</label>
                                    <textarea
                                        v-model="chapterForm.content"
                                        placeholder="Write chapter content here..."
                                        class="bg-gray-900 border border-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none min-h-[200px] resize-y"
                                        required
                                    />
                                </div>

                                <div class="flex gap-2">
                                    <button
                                        type="submit"
                                        :disabled="chapterForm.processing"
                                        class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2 rounded-lg text-sm transition-all disabled:bg-gray-600 disabled:cursor-not-allowed"
                                    >
                                        {{ chapterForm.processing ? 'Saving...' : '✓ Save Chapter' }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeAddChapter"
                                        class="bg-gray-700 hover:bg-gray-600 text-white font-semibold px-5 py-2 rounded-lg text-sm transition-all"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Add Chapter button -->
                        <div v-else class="p-3 border-t border-gray-800 flex justify-end">
                            <button
                                @click="openAddChapter(vol.id)"
                                class="flex items-center gap-2 text-xs font-semibold bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white px-4 py-2 rounded-lg transition-all border border-gray-700"
                            >
                                <Plus class="w-3.5 h-3.5" />
                                Add Chapter
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </DashboardLayout>

    <!-- ── ADD VOLUME MODAL ─────────────────── -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="showAddVolume"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
                @click.self="showAddVolume = false"
            >
                <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-white">Add New Volume</h2>
                        <button @click="showAddVolume = false" class="text-gray-500 hover:text-white transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>
                    <form @submit.prevent="submitVolume" class="space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-300">Volume Number</label>
                            <input
                                v-model="volumeForm.no"
                                type="number"
                                :placeholder="String(nextVolumeNo())"
                                class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                required min="1"
                            >
                            <p v-if="volumeForm.errors.no" class="text-red-400 text-xs">{{ volumeForm.errors.no }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button
                                type="submit"
                                :disabled="volumeForm.processing"
                                class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg transition-all disabled:bg-gray-600 disabled:cursor-not-allowed"
                            >
                                {{ volumeForm.processing ? 'Adding...' : 'Add Volume' }}
                            </button>
                            <button
                                type="button"
                                @click="showAddVolume = false"
                                class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-2.5 rounded-lg transition-all"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- ── EDIT VOLUME MODAL ─────────────────── -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="showEditVolume"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
                @click.self="showEditVolume = false"
            >
                <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-white">Edit Volume</h2>
                        <button @click="showEditVolume = false" class="text-gray-500 hover:text-white transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>
                    <form @submit.prevent="submitEditVolume" class="space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-300">Volume Number</label>
                            <input
                                v-model="volumeEditForm.no"
                                type="number"
                                class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                required min="1"
                            >
                            <p v-if="volumeEditForm.errors.no" class="text-red-400 text-xs">{{ volumeEditForm.errors.no }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button
                                type="submit"
                                :disabled="volumeEditForm.processing"
                                class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg transition-all disabled:bg-gray-600 disabled:cursor-not-allowed"
                            >
                                {{ volumeEditForm.processing ? 'Updating...' : 'Update Volume' }}
                            </button>
                            <button
                                type="button"
                                @click="showEditVolume = false"
                                class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-2.5 rounded-lg transition-all"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>