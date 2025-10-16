import { defineStore } from 'pinia'
import authService from '../services/authService'
import router from '../router' // ← TAMBAHKAN BARIS INI (import router)

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    isAuthenticated: !!localStorage.getItem('token')
  }),

  getters: {
    getUser: (state) => state.user,
    isLoggedIn: (state) => state.isAuthenticated
  },

  actions: {
    async login(credentials) {
      try {
        const response = await authService.login(credentials)
        
        if (response.data.success) {
          this.token = response.data.data.token
          this.user = response.data.data.user
          this.isAuthenticated = true

          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))
          
          return response.data
        }
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    async register(userData) {
      try {
        const response = await authService.register(userData)
        
        if (response.data.success) {
          this.token = response.data.data.token
          this.user = response.data.data.user
          this.isAuthenticated = true

          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))
          
          return response.data
        }
      } catch (error) {
        throw error
      }
    },

    async logout() {
      try {
        if (this.token) {
          await authService.logout()
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
        router.push('/login') // ← TAMBAHKAN BARIS INI (redirect ke login)
      }
    },

    async checkAuth() {
      const token = localStorage.getItem('token')
      
      if (token) {
        try {
          const response = await authService.getUser()
          
          if (response.data.success) {
            this.user = response.data.data
            this.isAuthenticated = true
            localStorage.setItem('user', JSON.stringify(this.user))
          }
        } catch (error) {
          console.error('Auth check failed:', error)
          this.clearAuth()
        }
      } else {
        this.clearAuth()
      }
    },

    clearAuth() {
      this.token = null
      this.user = null
      this.isAuthenticated = false
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }
})