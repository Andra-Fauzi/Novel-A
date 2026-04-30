<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <nav class="w-full">
    <div class="flex justify-between p-5 items-center">
      
      <Link :href="route('home')">
        <span class="font-dancing-script text-5xl">Novel-A</span>
      </Link>

      <ul class="flex gap-3 items-center"> <!-- penting -->

        <!-- Kalau belum login -->
        <template v-if="!$page.props.auth.user">
          <li>
            <Link :href="route('login')" class="bg-blue-500 rounded text-white hover:bg-blue-700 font-bold py-2 px-4">
              Login
            </Link>
          </li>
          <li>
            <Link :href="route('register')" class="bg-blue-500 rounded text-white hover:bg-blue-700 font-bold py-2 px-4">
              Register
            </Link>
          </li>
        </template>
      
        <!-- Kalau sudah login -->
        <template v-else>
          <li class="text-center">
            <h1 class="underline font-bold text-xl">
              {{ $page.props.auth.user.name }}
            </h1>
            <h1 v-if="$page.props.auth.user.role === 'author'">Author</h1>
            <h1 v-if="$page.props.auth.user.role === 'admin'">Admin</h1>
        </li>

          <li>
            <Link :href="route('home')" class="bg-blue-500 rounded text-white hover:bg-blue-700 font-bold py-2 px-4">
              Home
            </Link>
          </li>

          <li>
            <Link :href="route('dashboard')" class="bg-blue-500 rounded text-white hover:bg-blue-700 font-bold py-2 px-4">
              Dashboard
            </Link>
          </li>

          <li>
            <Link :href="route('logout')" method="post" class="bg-blue-500 rounded text-white hover:bg-blue-700 font-bold py-2 px-4">
              Logout    
            </Link>
          </li>
        </template>

      </ul>
    </div>
  </nav>
</template>