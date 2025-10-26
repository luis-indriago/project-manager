<template>
  <form @submit.prevent="submit">
    <!-- Nombre -->
    <div class="mb-4">
      <label class="block mb-1 font-bold">Nombre</label>
      <input
        v-model="form.name"
        type="text"
        class="border w-full p-2 rounded"
        :class="{ 'border-red-500': fieldError('name') }"
      />
      <small v-if="fieldError('name')" class="text-red-600">{{ fieldError('name') }}</small>
    </div>

    <!-- Descripción -->
    <div class="mb-4">
      <label class="block mb-1 font-bold">Descripción</label>
      <textarea
        v-model="form.description"
        class="border w-full p-2 rounded"
        :class="{ 'border-red-500': fieldError('description') }"
      ></textarea>
      <small v-if="fieldError('description')" class="text-red-600">{{ fieldError('description') }}</small>
    </div>

    <!-- Botón -->
    <button
      type="submit"
      class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
    >
      {{ isEditing ? 'Actualizar' : 'Crear' }}
    </button>
  </form>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'
import { useProjectStore } from '../stores/projects'

const props = defineProps({ project: { type: Object, default: null } })
const emit = defineEmits(['saved'])

const store = useProjectStore()

const form = reactive({ name: '', description: '' })
const errors = reactive({}) // { name: string, description: string }

const isEditing = computed(() => !!props.project)

/* ---------- helpers ---------- */
function fieldError(field) {
  return errors[field] || ''
}

function setErrors(errObject) {
  Object.keys(errors).forEach(k => (errors[k] = ''))
  Object.assign(errors, errObject)
}

/* ---------- carga / limpieza ---------- */
watch(
  () => props.project,
  p => {
    if (p) {
      form.name = p.name || ''
      form.description = p.description || ''
    } else {
      form.name = ''
      form.description = ''
    }
    setErrors({})
  },
  { immediate: true }
)

/* ---------- submit ---------- */
async function submit() {
  setErrors({})

  try {
    if (isEditing.value) {
      await store.updateProject(props.project.id, form)
    } else {
      await store.createProject(form)
    }
    emit('saved')
  } catch (err) {
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