<template>
  <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition relative cursor-grab">
    <span
      class="absolute top-2 right-2 px-2 py-1 rounded text-xs font-semibold text-white"
      :class="{
        'bg-yellow-400': task.status === 'pending',
        'bg-blue-500': task.status === 'in_progress',
        'bg-green-500': task.status === 'completed'
      }"
    >
      {{ statusLabel }}
    </span>

    <h3 class="font-semibold text-lg mb-1">{{ task.title }}</h3>
    <p class="text-gray-600 text-sm mb-2">{{ task.description }}</p>
    <p class="text-gray-500 text-xs mb-2">
      Asignado a: {{ task.assigned_user?.name || 'Sin asignar' }}
    </p>

    <!-- Botón editar -->
    <button
      @click.stop="$emit('edit-task', task)"
      class="absolute bottom-2 right-2 p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-yellow-600"
      title="Editar tarea"
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

    <!-- Botón borrar -->
    <button
        @click.stop="$emit('delete-task', task)" 
        class="absolute bottom-2 right-10 p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-red-600"
        title="Eliminar tarea"
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
</template>

<script setup>

import { computed } from 'vue'

const props = defineProps({
  task: Object
})

const statusLabel = computed(() => {
  switch (props.task.status) {
    case 'pending': return 'Pendiente'
    case 'in_progress': return 'En progreso'
    case 'completed': return 'Completada'
    default: return props.task.status
  }
})
</script>
