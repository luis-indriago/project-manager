<template>
  <div
    class="bg-white rounded-xl shadow hover:shadow-lg transition-all duration-200 overflow-hidden group cursor-pointer"
    @click="$emit('view', project.id)"
  >
    <!-- Contenido -->
    <div class="p-5">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">
        {{ project.name }}
      </h3>
      <p class="text-gray-600 text-sm mb-4 line-clamp-2">
        {{ project.description }}
      </p>

      <!-- Barra de progreso -->
      <div class="mb-4">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
          <span>Progreso</span>
          <span>{{ project.progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 h-1.5 rounded-full">
          <div
            class="bg-blue-500 h-1.5 rounded-full"
            :style="{ width: project.progress + '%' }"
          />
        </div>
      </div>
    </div>

    <!-- Acciones (iconos) -->
    <div class="flex items-center justify-end gap-2 px-5 pb-4">
      <button
        @click.stop="$emit('edit', project)"
        class="p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-yellow-600"
        title="Editar"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.232 3.232z"
          />
        </svg>
      </button>

      <button
        @click.stop="$emit('delete', project.id)"
        class="p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-red-600"
        title="Eliminar"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  project: { type: Object, required: true },
})
defineEmits(['view', 'edit', 'delete'])
</script>