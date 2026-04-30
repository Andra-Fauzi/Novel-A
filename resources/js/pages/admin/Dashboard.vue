<script setup lang="ts">
import { computed, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import SidebarDashboardButton from '@/components/SidebarDashboardButton.vue';
import {
    LayoutDashboard, BookText, Users, UserCheck,
    Trash2, Edit2, X, Search, ChevronUp, ChevronDown, Image
} from 'lucide-vue-next';

const props = defineProps<{
    adminData: { users: number; novels: number; authors: number; comments: number };
    novels: any[];
    authors: any[];
    users: any[];
    carousels: any[];
}>();

// ── Sidebar state ────────────────────────────────
const currentView = ref('Overview');

// ── Carousel Management ──────────────────────────
const carouselForm = useForm({
    image: null as File | null,
    link: '',
});

const onCarouselImageChange = (e: any) => {
    carouselForm.image = e.target.files[0];
};

const submitCarousel = () => {
    carouselForm.post(route('admin.carousel.store'), {
        onSuccess: () => { carouselForm.reset(); },
    });
};

const deleteCarousel = (id: number) => {
    if (confirm('Delete this carousel item?')) {
        router.delete(route('admin.carousel.delete', id));
    }
};

// ── Novels search & sort ─────────────────────────
const novelSearch = ref('');
const novelSort = ref<{ key: string; dir: 'asc' | 'desc' }>({ key: 'created_at', dir: 'desc' });

const toggleSort = (key: string) => {
    if (novelSort.value.key === key) {
        novelSort.value.dir = novelSort.value.dir === 'asc' ? 'desc' : 'asc';
    } else {
        novelSort.value = { key, dir: 'asc' };
    }
};

const filteredNovels = computed(() => {
    let list = [...props.novels];
    const q = novelSearch.value.toLowerCase();
    if (q) list = list.filter(n => n.title.toLowerCase().includes(q) || n.author?.author_name?.toLowerCase().includes(q));
    list.sort((a, b) => {
        const av = a[novelSort.value.key] ?? '';
        const bv = b[novelSort.value.key] ?? '';
        const cmp = String(av).localeCompare(String(bv));
        return novelSort.value.dir === 'asc' ? cmp : -cmp;
    });
    return list;
});

// ── Users search ─────────────────────────────────
const userSearch = ref('');
const filteredUsers = computed(() => {
    const q = userSearch.value.toLowerCase();
    if (!q) return props.users;
    return props.users.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
});

// ── Edit Novel Modal ─────────────────────────────
const editingNovel = ref<any>(null);
const editForm = useForm({ title: '', description: '', status: 'Ongoing' });

const openEdit = (novel: any) => {
    editingNovel.value = novel;
    editForm.title = novel.title;
    editForm.description = novel.description ?? '';
    editForm.status = novel.status ?? 'Ongoing';
};

const closeEdit = () => { editingNovel.value = null; editForm.reset(); };

const submitEdit = () => {
    editForm.patch(route('admin.novel.update', editingNovel.value.id), {
        onSuccess: () => closeEdit(),
    });
};

// ── Delete Novel ─────────────────────────────────
const confirmDelete = (novel: any) => {
    if (confirm(`Delete "${novel.title}"? This action cannot be undone.`)) {
        router.delete(route('admin.novel.delete', novel.id));
    }
};

// ── Stat card config ─────────────────────────────
const stats = computed(() => [
    { label: 'Total Users',   value: props.adminData.users,    icon: '👤', color: 'from-blue-700 to-blue-900',   border: 'border-blue-700/40' },
    { label: 'Total Novels',  value: props.adminData.novels,   icon: '📚', color: 'from-purple-700 to-purple-900', border: 'border-purple-700/40' },
    { label: 'Total Authors', value: props.adminData.authors,  icon: '✍️',  color: 'from-green-700 to-green-900',  border: 'border-green-700/40' },
    { label: 'Comments',      value: props.adminData.comments, icon: '💬', color: 'from-orange-700 to-orange-900', border: 'border-orange-700/40' },
]);

// ── Sort indicator ───────────────────────────────
const SortIcon = (key: string) => {
    if (novelSort.value.key !== key) return null;
    return novelSort.value.dir === 'asc' ? ChevronUp : ChevronDown;
};
</script>

<template>
    <DashboardLayout>
        <!-- ── SIDEBAR ───────────────────────── -->
        <template #sidebar>
            <div class="mb-4 px-2">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-2 px-2">Admin Panel</p>
                <SidebarDashboardButton
                    :main="currentView" :change-main="(n: string) => currentView = n"
                    button-name="Overview" :icon="LayoutDashboard"
                />
                <SidebarDashboardButton
                    :main="currentView" :change-main="(n: string) => currentView = n"
                    button-name="Novels" :icon="BookText"
                />
                <SidebarDashboardButton
                    :main="currentView" :change-main="(n: string) => currentView = n"
                    button-name="Authors" :icon="UserCheck"
                />
                <SidebarDashboardButton
                    :main="currentView" :change-main="(n: string) => currentView = n"
                    button-name="Users" :icon="Users"
                />
                <SidebarDashboardButton
                    :main="currentView" :change-main="(n: string) => currentView = n"
                    button-name="Carousels" :icon="Image"
                />
            </div>

            <!-- Stats mini summary -->
            <div class="mt-auto px-4 py-3 bg-gray-800/50 rounded-lg mx-2 grid grid-cols-2 gap-2">
                <div class="text-center">
                    <p class="text-lg font-bold text-white">{{ adminData.novels }}</p>
                    <p class="text-xs text-gray-500">Novels</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-white">{{ adminData.users }}</p>
                    <p class="text-xs text-gray-500">Users</p>
                </div>
            </div>
        </template>

        <!-- ── HEADER ────────────────────────── -->
        <template #header>
            <div>
                <h1 class="text-xl font-bold text-white">
                    <span v-if="currentView === 'Overview'">🛡️ Admin Overview</span>
                    <span v-else-if="currentView === 'Novels'">📚 Novel Management</span>
                    <span v-else-if="currentView === 'Authors'">✍️ Authors</span>
                    <span v-else-if="currentView === 'Users'">👥 Users</span>
                    <span v-else-if="currentView === 'Carousels'">🖼️ Carousels</span>
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    <span v-if="currentView === 'Overview'">Platform statistics at a glance</span>
                    <span v-else-if="currentView === 'Novels'">View, edit, and delete any novel</span>
                    <span v-else-if="currentView === 'Authors'">All registered authors and their works</span>
                    <span v-else-if="currentView === 'Users'">All registered users</span>
                    <span v-else-if="currentView === 'Carousels'">Manage banner images for the home page</span>
                </p>
            </div>
        </template>

        <!-- ── MAIN ──────────────────────────── -->
        <template #default>

            <!-- ─ OVERVIEW ─ -->
            <div v-if="currentView === 'Overview'" class="p-6 space-y-6">
                <!-- Stat cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div
                        v-for="stat in stats" :key="stat.label"
                        class="relative overflow-hidden rounded-2xl border p-5 flex items-center gap-4 shadow-lg"
                        :class="stat.border + ' bg-gradient-to-br ' + stat.color"
                    >
                        <div class="text-4xl">{{ stat.icon }}</div>
                        <div>
                            <p class="text-white/60 text-xs font-semibold uppercase tracking-wide">{{ stat.label }}</p>
                            <p class="text-white text-3xl font-extrabold leading-none mt-1">{{ stat.value }}</p>
                        </div>
                        <!-- Decorative blob -->
                        <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full bg-white/5"></div>
                    </div>
                </div>

                <!-- Recent novels table preview -->
                <div class="bg-gray-900 border border-gray-700 rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-800 flex items-center justify-between">
                        <h2 class="font-bold text-white">Recent Novels</h2>
                        <button
                            @click="currentView = 'Novels'"
                            class="text-blue-400 hover:text-blue-300 text-sm font-semibold transition-colors"
                        >View all →</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-500 border-b border-gray-800 text-xs uppercase tracking-wide">
                                    <th class="text-left px-5 py-3">Title</th>
                                    <th class="text-left px-5 py-3">Author</th>
                                    <th class="text-left px-5 py-3">Status</th>
                                    <th class="text-right px-5 py-3">Reads</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="novel in novels.slice(0, 8)" :key="novel.id"
                                    class="border-b border-gray-800/60 hover:bg-gray-800/40 transition-colors">
                                    <td class="px-5 py-3 text-white font-medium max-w-[180px] truncate">{{ novel.title }}</td>
                                    <td class="px-5 py-3 text-gray-400">{{ novel.author?.author_name ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full"
                                            :class="{
                                                'bg-green-900/60 text-green-400': novel.status === 'Ongoing',
                                                'bg-blue-900/60 text-blue-400': novel.status === 'Completed',
                                                'bg-yellow-900/60 text-yellow-400': novel.status === 'Hiatus',
                                            }"
                                        >{{ novel.status ?? 'Ongoing' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right text-gray-400">{{ novel.readed ?? 0 }}</td>
                                </tr>
                                <tr v-if="!novels.length">
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-600">No novels found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ─ NOVELS ─ -->
            <div v-else-if="currentView === 'Novels'" class="p-6 space-y-4">
                <!-- Search bar -->
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" />
                    <input
                        v-model="novelSearch"
                        type="text"
                        placeholder="Search by title or author..."
                        class="w-full bg-gray-900 border border-gray-700 text-white pl-10 pr-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                    >
                </div>

                <div class="bg-gray-900 border border-gray-700 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-500 border-b border-gray-800 text-xs uppercase tracking-wide bg-gray-800/30">
                                    <th class="text-left px-5 py-3 w-10">#</th>
                                    <th class="text-left px-5 py-3">
                                        <button class="flex items-center gap-1 hover:text-gray-300 transition-colors" @click="toggleSort('title')">
                                            Title
                                            <component :is="SortIcon('title')" v-if="SortIcon('title')" class="w-3 h-3" />
                                        </button>
                                    </th>
                                    <th class="text-left px-5 py-3">Author</th>
                                    <th class="text-left px-5 py-3">Status</th>
                                    <th class="text-right px-5 py-3">
                                        <button class="flex items-center gap-1 hover:text-gray-300 transition-colors ml-auto" @click="toggleSort('readed')">
                                            Reads
                                            <component :is="SortIcon('readed')" v-if="SortIcon('readed')" class="w-3 h-3" />
                                        </button>
                                    </th>
                                    <th class="text-right px-5 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(novel, i) in filteredNovels" :key="novel.id"
                                    class="border-b border-gray-800/60 hover:bg-gray-800/40 transition-colors">
                                    <td class="px-5 py-3 text-gray-600 text-xs">{{ i + 1 }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                :src="novel.cover ? '/storage/' + novel.cover : 'https://placehold.co/40x56/1f2937/6b7280?text='"
                                                class="w-8 h-11 object-cover rounded shadow shrink-0"
                                                :alt="novel.title"
                                            >
                                            <span class="text-white font-medium max-w-[200px] truncate">{{ novel.title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-400">{{ novel.author?.author_name ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full"
                                            :class="{
                                                'bg-green-900/60 text-green-400': (novel.status ?? 'Ongoing') === 'Ongoing',
                                                'bg-blue-900/60 text-blue-400': novel.status === 'Completed',
                                                'bg-yellow-900/60 text-yellow-400': novel.status === 'Hiatus',
                                            }"
                                        >{{ novel.status ?? 'Ongoing' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right text-gray-400">{{ novel.readed ?? 0 }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                @click="openEdit(novel)"
                                                class="flex items-center gap-1.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-gray-200 py-1.5 px-3 rounded-lg transition-all"
                                            >
                                                <Edit2 class="w-3 h-3" /> Edit
                                            </button>
                                            <button
                                                @click="confirmDelete(novel)"
                                                class="flex items-center gap-1.5 text-xs font-semibold bg-red-900/50 hover:bg-red-800 text-red-300 py-1.5 px-3 rounded-lg transition-all"
                                            >
                                                <Trash2 class="w-3 h-3" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!filteredNovels.length">
                                    <td colspan="6" class="px-5 py-10 text-center text-gray-600">No novels found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-3 border-t border-gray-800 text-xs text-gray-500">
                        Showing {{ filteredNovels.length }} of {{ novels.length }} novels
                    </div>
                </div>
            </div>

            <!-- ─ AUTHORS ─ -->
            <div v-else-if="currentView === 'Authors'" class="p-6">
                <div class="bg-gray-900 border border-gray-700 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-500 border-b border-gray-800 text-xs uppercase tracking-wide bg-gray-800/30">
                                    <th class="text-left px-5 py-3">#</th>
                                    <th class="text-left px-5 py-3">Author Name</th>
                                    <th class="text-right px-5 py-3">Novels</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(author, i) in authors" :key="author.id"
                                    class="border-b border-gray-800/60 hover:bg-gray-800/40 transition-colors">
                                    <td class="px-5 py-3 text-gray-600 text-xs">{{ i + 1 }}</td>
                                    <td class="px-5 py-3">
                                        <p class="text-white font-medium">{{ author.author_name }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <span class="bg-blue-900/40 text-blue-400 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            {{ author.novels_count ?? 0 }} novel(s)
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!authors.length">
                                    <td colspan="3" class="px-5 py-10 text-center text-gray-600">No authors found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-3 border-t border-gray-800 text-xs text-gray-500">
                        {{ authors.length }} author(s) total
                    </div>
                </div>
            </div>

            <!-- ─ USERS ─ -->
            <div v-else-if="currentView === 'Users'" class="p-6 space-y-4">
                <!-- Search -->
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" />
                    <input
                        v-model="userSearch"
                        type="text"
                        placeholder="Search by name or email..."
                        class="w-full bg-gray-900 border border-gray-700 text-white pl-10 pr-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                    >
                </div>
                <div class="bg-gray-900 border border-gray-700 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-500 border-b border-gray-800 text-xs uppercase tracking-wide bg-gray-800/30">
                                    <th class="text-left px-5 py-3">#</th>
                                    <th class="text-left px-5 py-3">Name</th>
                                    <th class="text-left px-5 py-3">Email</th>
                                    <th class="text-left px-5 py-3">Role</th>
                                    <th class="text-right px-5 py-3">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user, i) in filteredUsers" :key="user.id"
                                    class="border-b border-gray-800/60 hover:bg-gray-800/40 transition-colors">
                                    <td class="px-5 py-3 text-gray-600 text-xs">{{ i + 1 }}</td>
                                    <td class="px-5 py-3 text-white font-medium">{{ user.name }}</td>
                                    <td class="px-5 py-3 text-gray-400">{{ user.email }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                            :class="{
                                                'bg-red-900/50 text-red-400': user.role === 'admin',
                                                'bg-purple-900/50 text-purple-400': user.role === 'author',
                                                'bg-gray-700/60 text-gray-300': !user.role || user.role === 'user',
                                            }"
                                        >{{ user.role ?? 'user' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right text-gray-500 text-xs">
                                        {{ new Date(user.created_at).toLocaleDateString('en-GB') }}
                                    </td>
                                </tr>
                                <tr v-if="!filteredUsers.length">
                                    <td colspan="5" class="px-5 py-10 text-center text-gray-600">No users found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-3 border-t border-gray-800 text-xs text-gray-500">
                        Showing {{ filteredUsers.length }} of {{ users.length }} users
                    </div>
                </div>
            </div>

            <!-- ─ CAROUSELS TAB ─ -->
            <div v-else-if="currentView === 'Carousels'" class="p-6 space-y-6">
                <!-- Add Form -->
                <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 shadow-lg shadow-black/20">
                    <h2 class="text-lg font-bold text-white mb-4">Add New Carousel</h2>
                    <form @submit.prevent="submitCarousel" class="space-y-4">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-300 mb-1.5">Image (Banner)</label>
                                <input type="file" @change="onCarouselImageChange" class="w-full text-sm text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition-colors" required accept="image/*" />
                                <p v-if="carouselForm.errors.image" class="text-red-400 text-xs mt-1.5">{{ carouselForm.errors.image }}</p>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-300 mb-1.5">Link URL (Optional)</label>
                                <input v-model="carouselForm.link" type="text" placeholder="e.g. /detail/1" class="w-full bg-gray-800 border border-gray-700 text-white p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all" />
                            </div>
                        </div>
                        <div class="pt-2">
                            <button type="submit" :disabled="carouselForm.processing" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-2.5 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ carouselForm.processing ? 'Uploading...' : 'Upload Carousel' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Carousel List -->
                <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6">
                    <h2 class="text-lg font-bold text-white mb-4">Active Carousels ({{ carousels.length }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        <div v-for="c in carousels" :key="c.id" class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden relative group shadow-md">
                            <div class="w-full h-40 bg-gray-900">
                                <img :src="'/storage/' + c.image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            </div>
                            <div class="p-4 flex items-center justify-between">
                                <p class="text-sm text-gray-400 truncate pr-4">
                                    <span class="font-semibold text-gray-300">Link:</span> {{ c.link || 'None' }}
                                </p>
                                <button @click="deleteCarousel(c.id)" class="text-gray-500 hover:text-red-400 bg-gray-900 hover:bg-gray-800 p-2 rounded-lg transition-colors border border-gray-700" title="Delete">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <div v-if="!carousels.length" class="col-span-full py-16 text-center text-gray-500 bg-gray-800/30 rounded-xl border border-dashed border-gray-700">
                            <Image class="w-12 h-12 mx-auto text-gray-600 mb-3" />
                            No carousels uploaded yet.
                        </div>
                    </div>
                </div>
            </div>

        </template>
    </DashboardLayout>

    <!-- ── EDIT NOVEL MODAL ─────────────────── -->
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
                                class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                                type="text" required
                            >
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-300">Description</label>
                            <textarea
                                v-model="editForm.description"
                                class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none min-h-[100px] resize-none"
                            />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-300">Status</label>
                            <select
                                v-model="editForm.status"
                                class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
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