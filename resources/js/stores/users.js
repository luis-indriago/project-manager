import { defineStore } from 'pinia'
import api from '../services/api'

export const useUserStore = defineStore('users', {
  state: () => ({
    projects: [],
    loading: false,
  }),
  actions: {
   async fetchAllUsers() {
        try {
            const { data } = await api.get('/users/all')
            return data.data

        } catch (err) {
            throw err.response?.data?.message || 'Error al cargar usuarios'
        }
    },

    async fetchUsers({ page = 1, search = '' } = {}) {
      this.loading = true
      try {
        const res = await api.get('/users', { params: { page, search } })

        // ⬅️ accedemos un nivel más
        this.users = res.data.data.data ?? [] // array de usuarios
        this.meta  = {
          current_page: res.data.data.current_page,
          last_page:    res.data.data.last_page,
          total:        res.data.data.total,
        }
      } catch (err) {
        this.users = []
        this.meta  = {}
        throw err.response?.data?.message || 'Error al cargar usuarios'
      } finally {
        this.loading = false
      }
    },

    async createUser(payload) {
      try {
        const { data } = await api.post('/users', payload)
        return data.data
      } catch (err) {
        throw err.response?.data ?? { message: 'Error al crear usuario' }
      }
    },

    async updateUser(id, payload) {
      try {
        const { data } = await api.put(`/users/${id}`, payload)
        return data.data
      } catch (err) {
        throw err.response?.data ?? { message: 'Error al actualizar usuario' }
      }
    },

    async deleteUser(id) {
      await api.delete(`/users/${id}`)
    },
  }
})
