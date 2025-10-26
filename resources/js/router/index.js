import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Projects from '../views/Projects.vue'
import Users from '../views/Users.vue'
import ProjectView from '../views/ProjectView.vue'
import { useAuthStore } from '../stores/auth'

const routes = [
  { path: '/login', component: Login },
  { 
    path: '/projects', 
    component: Projects, 
    meta: { requiresAuth: true } 
  },
  {
    path: '/projects/:id',
    name: 'ProjectView',
    component: ProjectView,
    meta: { requiresAuth: true }
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
    meta: { requiresAuth: true }
  },
  /*{
    path: '/:pathMatch(.*)*',
    redirect: '/login'
  }*/
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, _, next) => {
  const auth = useAuthStore() // Pinia sigue vivo
  const publicPages = ['/login', '/register']
  const needsAuth   = !publicPages.includes(to.path)

  // comprobamos el token que ESTÁ en localStorage (sin await)
  const token = localStorage.getItem('token')

  if (needsAuth && !token) {
    next('/login')
  } else {
    next()
  }
})

export default router
