<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';

const emit = defineEmits<{
    (e: 'success'): void;
}>();

const page = usePage();

const form = useForm({
    title: '',
    author_id: null as number | null,
    cover: null as File | null,
    description: '',
});

const upload = () => {
    form.author_id = page.props.auth?.user?.id;

    form.post('/upload_novel', {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            emit('success');
        },
        onError: (err) => {
            console.error('Failed to create novel:', err);
        },
    });
};
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">
        <form @submit.prevent="upload" class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-8 space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-700 pb-4">
                <h2 class="text-2xl font-bold text-white">Upload New Novel</h2>
                <p class="text-gray-400 text-sm mt-1">Publish your story and start building your audience.</p>
            </div>

            <!-- Title -->
            <div class="flex flex-col gap-2">
                <label for="title" class="text-sm font-semibold text-gray-300">Novel Title</label>
                <input
                    v-model="form.title"
                    placeholder="Enter novel title..."
                    class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all placeholder:text-gray-500"
                    type="text" id="title" required
                >
                <p v-if="form.errors.title" class="text-red-400 text-xs">{{ form.errors.title }}</p>
            </div>

            <!-- Cover -->
            <div class="flex flex-col gap-2">
                <label for="cover" class="text-sm font-semibold text-gray-300">Cover Image</label>
                <input
                    @input="form.cover = ($event.target as HTMLInputElement).files?.[0] || null"
                    class="block w-full text-sm text-gray-400
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-full file:border-0
                           file:text-sm file:font-semibold
                           file:bg-blue-600 file:text-white
                           hover:file:bg-blue-500 file:cursor-pointer cursor-pointer
                           bg-gray-800 border border-gray-700 rounded-lg p-2"
                    type="file" id="cover" accept="image/*"
                />
                <p class="text-xs text-gray-500 italic">*Recommended size: 600×900px (JPG/PNG)</p>
            </div>

            <!-- Description -->
            <div class="flex flex-col gap-2">
                <label for="description" class="text-sm font-semibold text-gray-300">Synopsis / Description</label>
                <textarea
                    v-model="form.description"
                    placeholder="Describe your novel..."
                    class="bg-gray-800 border border-gray-700 text-white p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all placeholder:text-gray-500 min-h-[140px] resize-none"
                    id="description"
                />
            </div>

            <!-- Upload progress -->
            <div v-if="form.progress" class="w-full bg-gray-700 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" :style="`width: ${form.progress.percentage}%`"></div>
                <p class="text-xs text-right text-blue-400 mt-1">Uploading: {{ form.progress.percentage }}%</p>
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-lg shadow-lg transform active:scale-95 transition-all disabled:bg-gray-600 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <span v-if="form.processing" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
                    {{ form.processing ? 'Publishing...' : '🚀 Publish Novel Now' }}
                </button>
            </div>
        </form>
    </div>
</template>