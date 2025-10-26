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

    <!-- Email -->
    <div class="mb-4">
      <label class="block mb-1 font-bold">Email</label>
      <input
        v-model="form.email"
        type="email"
        class="border w-full p-2 rounded"
        :class="{ 'border-red-500': fieldError('email') }"
      />
      <small v-if="fieldError('email')" class="text-red-600">{{ fieldError('email') }}</small>
    </div>

    <!-- Contraseña -->
    <div class="mb-4">
      <label class="block mb-1 font-bold">Contraseña</label>
      <input
        v-model="form.password"
        type="password"
        class="border w-full p-2 rounded"
        :class="{ 'border-red-500': fieldError('password') }"
      />
      <small v-if="props.user" class="text-gray-500">Dejar vacío para mantener la actual.</small>
      <small v-if="fieldError('password')" class="text-red-600">{{ fieldError('password') }}</small>
    </div>

    <!-- Rol -->
    <div class="mb-4">
      <label class="block mb-1 font-bold">Rol</label>
      <select
        v-model="form.role"
        class="border w-full p-2 rounded"
        :class="{ 'border-red-500': fieldError('role') }"
      >
        <option value="admin">Administrador</option>
        <option value="developer">Desarrollador</option>
      </select>
      <small v-if="fieldError('role')" class="text-red-600">{{ fieldError('role') }}</small>
    </div>

    <!-- Botones -->
    <div class="flex justify-end gap-2">
      <button
        type="button"
        @click="$emit('cancel')"
        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
      >
        Cancelar
      </button>
      <button
        type="submit"
        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
      >
        {{ props.user ? 'Actualizar' : 'Crear' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { useUserStore } from '@/stores/users'

const props = defineProps({ user: Object })
const emit = defineEmits(['saved', 'cancel'])

const userStore = useUserStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  role: 'developer',
})

const errors = reactive({})

/* ---------- helpers ---------- */
function fieldError(field) {
  return errors[field] || ''
}

function setErrors(errObject) {
  Object.keys(errors).forEach(k => (errors[k] = ''))
  Object.assign(errors, errObject)
}

/* ---------- cargar datos al editar ---------- */
watch(
  () => props.user,
  u => {
    if (u) {
      form.name = u.name
      form.email = u.email
      form.password = ''
      form.role = u.roles?.find(r => r.name === 'admin' || r.name === 'developer')?.name ?? 'developer'
    } else {
      form.name = ''
      form.email = ''
      form.password = ''
      form.role = 'developer'
    }
    setErrors({})
  },
  { immediate: true }
)

/* ---------- enviar ---------- */
async function submit() {
  setErrors({})

  try {
    const payload = { ...form }
    if (!payload.password && props.user) delete payload.password

    if (props.user) {
      await userStore.updateUser(props.user.id, payload)
    } else {
      await userStore.createUser(payload)
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