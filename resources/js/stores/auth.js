import { defineStore } from 'pinia'
import api from '../services/api'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    loading: false,
  }),

  actions: {
    /* Se llama UNA vez al arrancar la aplicación */
    initAuth() {
      const raw = localStorage.getItem('user')
      const tkn = localStorage.getItem('token')
      if (raw && tkn) {
        this.user = JSON.parse(raw)
        this.token = tkn
      }
    },

    async login(credentials) {
      this.loading = true
      try {
        const { data } = await api.post('/login', credentials)
        this.user = data.data.user
        this.token = data.data.token
        localStorage.setItem('user', JSON.stringify(this.user))
        localStorage.setItem('token', this.token)
        router.push('/projects')
      } catch (e) {
        throw e.response?.data?.message || 'Error al iniciar sesión'
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.user = null
      this.token = null
      localStorage.clear()
      router.push('/login')
    },
  },
})