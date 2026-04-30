<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    main: string;
    buttonName: string;
    changeMain: (name: string) => void;
    icon?: any;
}>();

const isActive = computed(() => props.main === props.buttonName);
</script>

<template>
    <button
        @click="changeMain(buttonName)"
        class="flex items-center gap-3 w-full px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
        :class="isActive
            ? 'bg-blue-600 text-white shadow-md shadow-blue-900/40'
            : 'text-slate-500 dark:text-gray-400 hover:bg-slate-100 dark:hover:bg-gray-800 hover:text-slate-900 dark:hover:text-white'"
    >
        <component :is="icon" v-if="icon" class="w-4 h-4 shrink-0" />
        <span>{{ buttonName }}</span>
    </button>
</template>