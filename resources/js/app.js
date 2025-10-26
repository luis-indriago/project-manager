import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import { useAuthStore } from '@/stores/auth'
import '../css/app.css'

const pinia = createPinia()
const app = createApp(App)

app.use(pinia)

/* ⬅️ re-hidratar ANTES de que exista cualquier componente */
const auth = useAuthStore()
auth.initAuth()

app.use(router)
app.mount('#app')