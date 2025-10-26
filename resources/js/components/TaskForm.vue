<template>
  <form @submit.prevent="submit">
    <!-- Título -->
    <div class="mb-4">
      <label class="block mb-1 font-bold">Título</label>
      <input
        v-model="form.title"
        type="text"
        class="border w-full p-2 rounded"
        :class="{ 'border-red-500': fieldError('title') }"
      />
      <small v-if="fieldError('title')" class="text-red-600">{{ fieldError('title') }}</small>
    </div>

    <!-- Descripción -->
    <div class="mb-4">
      <label class="block mb-1 font-bold">Descripción</label>
      <textarea
        v-model="form.description"
        class="border w-full p-2 rounded"
        :class="{ 'border-red-500': fieldError('description') }"
      />
      <small v-if="fieldError('description')" class="text-red-600">{{ fieldError('description') }}</small>
    </div>

    <!-- Asignado -->
    <div class="mb-4">
      <label class="block mb-1 font-bold">Asignar a</label>
      <select
        v-model="form.assigned_to"
        class="border w-full p-2 rounded"
        :class="{ 'border-red-500': fieldError('assigned_to') }"
      >
        <option v-for="u in users" :key="u.id" :value="Number(u.id)" >
          {{ u.name }}
        </option>
      </select>
      <small v-if="fieldError('assigned_to')" class="text-red-600">{{ fieldError('assigned_to') }}</small>
    </div>

    <!-- Botón -->
    <button
      type="submit"
      class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700"
    >
      {{ task ? 'Actualizar' : 'Crear' }}
    </button>
  </form>
</template>

<script setup>
import { reactive, watch } from 'vue'
import { useProjectStore } from '@/stores/projects'

const props = defineProps({
  task: Object,
  projectId: Number,
  users: Array,
})
const emit = defineEmits(['saved'])

const store = useProjectStore()

const form = reactive({
  title: '',
  description: '',
  assigned_to: null,
})

// errores por campo { title: string, description: string, ... }
const errors = reactive({})

/* ---------- helpers ---------- */
function fieldError(field) {
  return errors[field] || ''
}

function setErrors(errObject) {
  // limpia anterior
  Object.keys(errors).forEach(k => (errors[k] = ''))
  // pone nuevos
  Object.assign(errors, errObject)
}

/* ---------- carga inicial ---------- */
watch(
  () => props.task,
  t => {
    if (t) {
      form.title = t.title
      form.description = t.description
      form.assigned_to = props.task?.assigned_user?.id ? Number(props.task.assigned_user.id) : null
    } else {
      form.title = ''
      form.description = ''
      form.assigned_to = null
    }
    setErrors({}) // limpia al cambiar
  },
  { immediate: true }
)

/* ---------- enviar ---------- */
async function submit() {
  setErrors({}) // limpia anterior

  try {
    if (props.task) {
      await store.updateTask(props.projectId, props.task.id, form)
    } else {
      await store.createTask(props.projectId, form)
    }
    emit('saved')
  } catch (err) {
    // err = { message: "...", errors: { title: [...], description: [...] } }
    if (err.errors) {
      const map = {}
      for (const field in err.errors) {
        map[field] = err.errors[field][0]
      }
      setErrors(map)
    } else {
      setErrors({ general: err.message || 'Error inesperado' })
    }
  }
}
</script>