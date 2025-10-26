// services/api.js
import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
})

/* ⬅️ token en CADA petición */
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

/* respuesta 401/403 → limpiar y redirigir */
api.interceptors.response.use(
  res => res,
  err => {
    if (err.response?.status === 401 || err.response?.status === 403) {
      localStorage.clear()
      window.location.href = '/login' // forzar refresco
    }
    return Promise.reject(err)
  }
)

export default api