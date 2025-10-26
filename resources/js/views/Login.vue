<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-2xl shadow-md w-96">
      <h1 class="text-2xl font-bold text-center mb-6">Iniciar sesión</h1>
      <form @submit.prevent="login">
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2">Correo</label>
          <input v-model="form.email" type="email" class="border rounded w-full py-2 px-3" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
          <input v-model="form.password" type="password" class="border rounded w-full py-2 px-3" />
        </div>
        <div v-if="error" class="text-red-600 text-sm mb-3">{{ error }}</div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
          Ingresar
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const form = ref({ email: 'luisindriago202@gmail.com', password: 'password' })
const error = ref(null)
const auth = useAuthStore()

const login = async () => {
  error.value = null
  try {
    await auth.login(form.value)
  } catch (e) {
    error.value = e
  }
}
</script>
