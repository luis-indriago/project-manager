import { defineStore } from 'pinia'
import api from '../services/api'

export const useProjectStore = defineStore('projects', {
  state: () => ({
    projects: [],
    loading: false,
  }),
  actions: {
    async fetchProjects() {
      this.loading = true
      try {
        const { data } = await api.get('/projects')
        this.projects = data.data || data
      } catch (err) {
        throw err.response?.data?.message || 'Error al cargar proyectos'
      } finally {
        this.loading = false
      }
    },

    async createProject(payload) {
      try {
        const { data } = await api.post('/projects', payload)
        this.projects.push(data.data)
        return data.data
      } catch (err) {
        throw err.response?.data ?? { message: 'Error al crear proyecto' }
      }
    },

    async updateProject(id, payload) {
      try {
        const { data } = await api.put(`/projects/${id}`, payload)
        const i = this.projects.findIndex(p => p.id === id)
        if (i !== -1) this.projects[i] = data.data
        return data.data
      } catch (err) {
        throw err.response?.data ?? { message: 'Error al actualizar proyecto' }
      }
    },

    async deleteProject(id) {
      try {
        await api.delete(`/projects/${id}`)
        this.projects = this.projects.filter(p => p.id !== id)
      } catch (err) {
        throw err.response?.data?.message || 'Error al eliminar proyecto'
      }
    },

    async fetchProjectDetail(id) {
      const { data } = await api.get(`/projects/${id}`)
      // Suponemos que tu backend devuelve algo como:
      // { project: {...}, tasks: [...] }
      return data.data || data
    },

    countTasks(project, status) {
      if (!project.tasks) return 0
      return project.tasks.filter(t => t.status === status).length
    },

    /* Tareas */
    async createTask(projectId, payload) {
    try {
        const { data } = await api.post('/tasks', { ...payload, project_id: projectId })
        const project = this.projects.find(p => p.id === projectId)
        if (project) project.tasks.push(data.data)
        return data.data
      } catch (err) {
        throw err.response?.data ?? { message: 'Error al crear tarea' }
      }
    },

    async updateTask(projectId, taskId, payload) {
      try {
        const { data } = await api.put(`/tasks/${taskId}`, { ...payload, project_id: projectId })
        const project = this.projects.find(p => p.id === projectId)
        if (project) {
          const i = project.tasks.findIndex(t => t.id === taskId)
          if (i !== -1) project.tasks[i] = data.data
        }
        return data.data
      } catch (err) {
        throw err.response?.data ?? { message: 'Error al actualizar tarea' }
      }
    },

    async updateTaskStatusOnly(taskId, newStatus) {
      try {
        const { data } = await api.patch(`/tasks/${taskId}/status`, { status: newStatus })
        return data.data
      } catch (err) {
        throw err.response?.data?.message || 'Error al cambiar estado'
      }
    },

    async deleteTask(taskId) {
      try {
        await api.delete(`/tasks/${taskId}`)

        // --- eliminar la tarea de cualquier proyecto que la tenga ---
        this.projects.forEach(p => {
          p.tasks = p.tasks.filter(t => t.id !== taskId)
        })

        return taskId // útil si quieres confirmar
      } catch (err) {
        throw err.response?.data?.message || 'Error al eliminar tarea'
      }
    }
  }
})
