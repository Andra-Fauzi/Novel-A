<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useDark, useToggle } from '@vueuse/core';
import { Sun, Moon } from 'lucide-vue-next';

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
  <nav class="w-full sticky top-0 z-50 backdrop-blur-xl bg-white/80 dark:bg-gray-950/80 border-b border-gray-200 dark:border-gray-800 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        
        <!-- Logo -->
        <Link :href="route('home')" class="flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-all duration-300 group-hover:-translate-y-0.5">
            <span class="font-dancing-script text-white text-2xl font-bold">N</span>
          </div>
          <span class="font-dancing-script text-4xl font-bold text-gray-900 dark:text-white tracking-wide group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">Novel-A</span>
        </Link>

        <!-- Links -->
        <ul class="flex items-center gap-2 md:gap-4 font-sans">
          
          <!-- Theme Toggle -->
          <li>
            <button @click="toggleDark()" class="p-2.5 rounded-full text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-400 dark:hover:text-blue-400 dark:hover:bg-blue-900/30 transition-all duration-300">
              <Sun v-if="isDark" class="w-5 h-5" />
              <Moon v-else class="w-5 h-5" />
            </button>
          </li>

          <!-- Kalau belum login -->
          <template v-if="!$page.props.auth.user">
            <li>
              <Link :href="route('login')" class="px-5 py-2.5 rounded-full text-gray-600 dark:text-gray-300 font-medium hover:text-blue-600 dark:hover:text-white hover:bg-blue-50 dark:hover:bg-white/10 transition-all duration-300">
                Login
              </Link>
            </li>
            <li>
              <Link :href="route('register')" class="px-6 py-2.5 bg-blue-600 text-white rounded-full font-semibold shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/40 hover:-translate-y-0.5 hover:bg-blue-500 transition-all duration-300">
                Register
              </Link>
            </li>
          </template>
        
          <!-- Kalau sudah login -->
          <template v-else>
            
            <li>
              <Link :href="route('home')" class="px-4 py-2 rounded-full text-gray-600 dark:text-gray-300 font-medium hover:text-blue-600 dark:hover:text-white hover:bg-blue-50 dark:hover:bg-white/10 transition-all duration-300">
                Home
              </Link>
            </li>

            <li>
              <Link :href="route('dashboard')" class="px-4 py-2 rounded-full text-gray-600 dark:text-gray-300 font-medium hover:text-blue-600 dark:hover:text-white hover:bg-blue-50 dark:hover:bg-white/10 transition-all duration-300">
                Dashboard
              </Link>
            </li>
            
            <li class="h-6 w-px bg-gray-300 dark:bg-gray-700 mx-2 hidden sm:block"></li>

            <li class="flex flex-col items-end mr-2 hidden sm:flex">
              <span class="font-bold text-sm text-gray-800 dark:text-gray-100 leading-tight">
                {{ $page.props.auth.user.name }}
              </span>
              <span class="text-[10px] px-2 py-0.5 mt-0.5 rounded-full bg-blue-500/20 text-blue-400 font-bold uppercase tracking-wider" v-if="['author', 'admin'].includes($page.props.auth.user.role)">
                {{ $page.props.auth.user.role === 'author' ? 'Author' : 'Admin' }}
              </span>
            </li>

            <li>
              <Link :href="route('logout')" method="post" as="button" class="ml-2 p-2 rounded-full text-gray-500 dark:text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-500/10 transition-all duration-300" title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
              </Link>
            </li>
          </template>

        </ul>
      </div>
    </div>
  </nav>
</template>