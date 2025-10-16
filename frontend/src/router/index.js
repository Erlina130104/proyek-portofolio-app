import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Import Pages
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Dashboard from '../pages/Dashboard.vue'
import Products from '../pages/Products.vue'
import Transactions from '../pages/Transactions.vue'
import Employees from '../pages/Employees.vue'
import Attendance from '../pages/Attendance.vue'
import Feedback from '../pages/Feedback.vue'
import Tickets from '../pages/Tickets.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { 
      requiresGuest: true,
      title: 'Login - SmartERP' 
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { 
      requiresGuest: true,
      title: 'Register - SmartERP' 
    }
  },
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { 
      requiresAuth: true,
      title: 'Dashboard - SmartERP' 
    }
  },
  {
    path: '/products',
    name: 'Products',
    component: Products,
    meta: { 
      requiresAuth: true,
      title: 'Products - SmartERP' 
    }
  },
  {
    path: '/transactions',
    name: 'Transactions',
    component: Transactions,
    meta: { 
      requiresAuth: true,
      title: 'Transactions - SmartERP' 
    }
  },
  {
    path: '/employees',
    name: 'Employees',
    component: Employees,
    meta: { 
      requiresAuth: true,
      title: 'Employees - SmartERP' 
    }
  },
  {
    path: '/attendance',
    name: 'Attendance',
    component: Attendance,
    meta: { 
      requiresAuth: true,
      title: 'Attendance - SmartERP' 
    }
  },
  {
    path: '/feedback',
    name: 'Feedback',
    component: Feedback,
    meta: { 
      requiresAuth: true,
      title: 'Feedback - SmartERP' 
    }
  },
  {
    path: '/tickets',
    name: 'Tickets',
    component: Tickets,
    meta: { 
      requiresAuth: true,
      title: 'Tickets - SmartERP' 
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Set page title
  document.title = to.meta.title || 'SmartERP'
  
  // Check auth hanya jika ada token
  if (!authStore.isLoggedIn && localStorage.getItem('token')) {
    await authStore.checkAuth()
  }
  
  const isAuthenticated = authStore.isLoggedIn
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)

  if (requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (requiresGuest && isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router