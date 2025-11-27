import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/employees',
    name: 'employees',
    component: () => import('../views/employee.vue')
  },
  {
    path: '/showemployees',
    name: 'showemployees',
    component: () => import('../views/showemployees.vue')
  },
  {
    path: '/addemployees',
    name: 'Addemployees',
    component: () => import('../views/addemployees.vue')
  }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router
