<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Overlay móvil -->
    <div
      v-if="showSidebar"
      class="fixed inset-0 bg-black/50 z-30 md:hidden"
      @click="toggleSidebar"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed z-40 top-0 left-0 h-full w-64 bg-white shadow-md flex flex-col transform transition-transform duration-300 ease-in-out',
        showSidebar ? 'translate-x-0' : '-translate-x-full',
        'md:translate-x-0 md:static md:flex'
      ]"
    >
      <div class="p-4 font-bold text-xl border-b flex justify-between items-center">
        ProjectManager
        <button class="md:hidden" @click="toggleSidebar">✕</button>
      </div>

      <nav class="flex-1 p-4 space-y-2">
        <router-link
          to="/projects"
          class="block py-2 px-3 rounded hover:bg-gray-200"
          :class="{ 'bg-gray-200 font-semibold': isActive('/projects') }"
        >
          Proyectos
        </router-link>

        <!-- ⬅️ visible SOLO si es admin -->
        <router-link
          v-if="isAdmin"
          to="/users"
          class="block py-2 px-3 rounded hover:bg-gray-200"
          :class="{ 'bg-gray-200 font-semibold': isActive('/users') }"
        >
          Usuarios
        </router-link>
      </nav>

      <div class="p-4 border-t">
        <button
          @click="logout"
          class="w-full py-2 px-3 rounded bg-red-500 hover:bg-red-600 text-white"
        >
          Cerrar sesión
        </button>
      </div>
    </aside>

    <!-- Contenido principal -->
    <main class="flex-1 p-6 overflow-auto">
      <button
        class="md:hidden mb-4 px-4 py-2 bg-gray-200 rounded"
        @click="toggleSidebar"
      >
        ☰
      </button>
      <slot />
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

/* ⬅️ calculado sobre datos reactivos que YA existen */
const isAdmin = computed(() => auth.user?.roles?.some(r => r.name === 'admin') ?? false)

const isActive = path => route.path.startsWith(path)

const logout = () => auth.logout()

const showSidebar = ref(false)
const toggleSidebar = () => (showSidebar.value = !showSidebar.value)
</script>