<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import SidebarDashboardButton from '@/components/SidebarDashboardButton.vue';
import FormCreateNovel from '@/components/FormCreateNovel.vue';
import { BookOpen, Plus, X, Edit2, Trash2, BookMarked, Upload } from 'lucide-vue-next';

const props = defineProps<{
    authorData: any;
}>();

// ── Sidebar state ────────────────────────────────
const currentView = ref('My Novels');

// ── Edit modal ──────────────────────────────────
const editingNovel = ref<any>(null);

const editForm = useForm({
    title: '',
    description: '',
    status: 'Ongoing',
});

const openEdit = (novel: any) => {
    editingNovel.value = novel;
    editForm.title = novel.title;
    editForm.description = novel.description;
    editForm.status = novel.status;
};

const closeEdit = () => {
    editingNovel.value = null;
    editForm.reset();
};

const submitEdit = () => {
    editForm.patch(route('author.novel.update', editingNovel.value.id), {
        onSuccess: () => closeEdit(),
    });
};

// ── Delete ──────────────────────────────────────
const confirmDelete = (novel: any) => {
    if (confirm(`Delete "${novel.title}"? This cannot be undone.`)) {
        router.delete(route('author.novel.delete', novel.id));
    }
};

// ── Upload success ───────────────────────────────
const uploadSuccess = ref(false);
const onUploadSuccess = () => {
    uploadSuccess.value = true;
    setTimeout(() => {
        uploadSuccess.value = false;
        currentView.value = 'My Novels';
    }, 2000);
};
</script>

<template>
    <DashboardLayout>
        <!-- ── SIDEBAR ───────────────────────── -->
        <template #sidebar="{ main, changeMain }">
            <div class="mb-4 px-2">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-2 px-2">Author Panel</p>
                <SidebarDashboardButton
                    :main="currentView"
                    :change-main="(n: string) => currentView = n"
                    button-name="My Novels"
                    :icon="BookMarked"
                />
                <SidebarDashboardButton
                    :main="currentView"
                    :change-main="(n: string) => currentView = n"
                    button-name="Upload Novel"
                    :icon="Upload"
                />
            </div>

            <!-- Author info -->
            <div class="mt-auto px-4 py-3 bg-gray-800/50 rounded-lg mx-2">
                <p class="text-xs text-gray-500">Logged in as</p>
                <p class="text-sm font-semibold text-white truncate">{{ authorData?.author_name ?? 'Author' }}</p>
                <p class="text-xs text-blue-400 mt-0.5">{{ authorData?.novels?.length ?? 0 }} novel(s) published</p>
            </div>
        </template>

        <!-- ── HEADER ────────────────────────── -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white">
                        {{ currentView === 'My Novels' ? '📚 My Novels' : '✍️ Upload Novel' }}
                    </h1>
                    <p class="text-gray-400 text-sm mt-0.5">
                        {{ currentView === 'My Novels'
                            ? 'Manage and update your published works'
                            : 'Create and publish a new novel' }}
                    </p>
                </div>
                <button
                    v-if="currentView === 'My Novels'"
                    @click="currentView = 'Upload Novel'"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-all shadow-md shadow-blue-900/30"
                >
                    <Plus class="w-4 h-4" />
                    New Novel
                </button>
            </div>
        </template>

        <!-- ── MAIN ──────────────────────────── -->
        <template #default>
            <!-- ─ MY NOVELS ─ -->
            <div v-if="currentView === 'My Novels'" class="p-6">
                <!-- Empty state -->
                <div v-if="!authorData?.novels?.length"
                    class="flex flex-col items-center justify-center py-24 text-center">
                    <BookOpen class="w-16 h-16 text-gray-700 mb-4" />
                    <h2 class="text-xl font-semibold text-gray-400">No novels yet</h2>
                    <p class="text-gray-600 text-sm mt-1">Click "New Novel" to publish your first story.</p>
                </div>

                <!-- Novel grid -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                    <div
                        v-for="novel in authorData.novels"
                        :key="novel.id"
                        class="bg-gray-900 border border-gray-700 rounded-2xl overflow-hidden shadow-lg hover:shadow-blue-900/20 hover:border-blue-700/50 transition-all duration-200 flex flex-col"
                    >
                        <!-- Cover + meta row -->
                        <div class="flex gap-4 p-4">
                            <img
                                :src="novel.cover ? '/storage/' + novel.cover : 'https://placehold.co/120x170/1f2937/6b7280?text=No+Cover'"
                                :alt="novel.title"
                                class="w-24 h-36 object-cover rounded-xl shrink-0 shadow-md"
                            >
                            <div class="flex flex-col justify-between min-w-0">
                                <div>
                                    <h2 class="font-bold text-white text-base leading-snug line-clamp-2">{{ novel.title }}</h2>
                                    <p class="text-gray-400 text-xs mt-1 line-clamp-3">{{ novel.description }}</p>
                                </div>
                                <!-- Status badge -->
                                <div class="mt-2">
                                    <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                        :class="{
                                            'bg-green-900/60 text-green-400': novel.status === 'Ongoing',
                                            'bg-blue-900/60 text-blue-400': novel.status === 'Completed',
                                            'bg-yellow-900/60 text-yellow-400': novel.status === 'Hiatus',
                                        }"
                                    >{{ novel.status }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats row -->
                        <div class="grid grid-cols-3 gap-0 border-t border-gray-800 text-center text-xs">
                            <div class="py-2.5 border-r border-gray-800">
                                <p class="text-gray-500">Volumes</p>
                                <p class="text-white font-bold">{{ novel.volumes_count ?? novel.volumes?.length ?? 0 }}</p>
                            </div>
                            <div class="py-2.5 border-r border-gray-800">
                                <p class="text-gray-500">Reads</p>
                                <p class="text-white font-bold">{{ novel.readed ?? 0 }}</p>
                            </div>
                            <div class="py-2.5">
                                <p class="text-gray-500">Stars</p>
                                <p class="text-white font-bold">{{ novel.stars ?? 0 }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 p-3 border-t border-gray-800">
                            <Link
                                :href="route('author.add', novel.id)"
                                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white py-2 px-3 rounded-lg transition-all"
                            >
                                <Plus class="w-3.5 h-3.5" />
                                Add Vol/Chapter
                            </Link>
                            <button
                                @click="openEdit(novel)"
                                class="flex items-center justify-center gap-1.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-gray-200 py-2 px-3 rounded-lg transition-all"
                            >
                                <Edit2 class="w-3.5 h-3.5" />
                                Edit
                            </button>
                            <button
                                @click="confirmDelete(novel)"
                                class="flex items-center justify-center gap-1.5 text-xs font-semibold bg-red-900/50 hover:bg-red-800 text-red-300 py-2 px-3 rounded-lg transition-all"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─ UPLOAD NOVEL ─ -->
            <div v-if="currentView === 'Upload Novel'">
                <!-- Success banner -->
                <Transition name="fade">
                    <div v-if="uploadSuccess"
                        class="mx-6 mt-6 bg-green-900/60 border border-green-700 text-green-300 px-5 py-3 rounded-xl text-sm font-semibold flex items-center gap-2">
                        ✅ Novel published successfully! Redirecting to My Novels...
                    </div>
                </Transition>
                <FormCreateNovel @success="onUploadSuccess" />
            </div>
        </template>
    </DashboardLayout>

    <!-- ── EDIT MODAL ────────────────────────── -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="editingNovel"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
                @click.self="closeEdit"
            >
                <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl w-full max-w-lg p-6 space-y-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Edit Novel</h2>
                        <button @click="closeEdit" class="text-gray-500 hover:text-white transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submitEdit" class="space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-300">Title</label>
                            <input
                                v-model="editForm.title"
                                class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                                type="text" required
                            >
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-300">Description</label>
                            <textarea
                                v-model="editForm.description"
                                class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all min-h-[100px] resize-none"
                            />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-300">Status</label>
                            <select
                                v-model="editForm.status"
                                class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                            >
                                <option>Ongoing</option>
                                <option>Completed</option>
                                <option>Hiatus</option>
                            </select>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button
                                type="submit"
                                :disabled="editForm.processing"
                                class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg transition-all disabled:bg-gray-600 disabled:cursor-not-allowed"
                            >
                                {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                            <button
                                type="button"
                                @click="closeEdit"
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