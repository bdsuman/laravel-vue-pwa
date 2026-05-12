import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: null,
    loading: false
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token
  },
  
  actions: {
    async login(email, password) {
      this.loading = true
      try {
        const { data } = await axios.post('/api/auth/login', { email, password })
        this.token = data.data.token
        localStorage.setItem('token', data.data.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        await this.fetchUser()
        return { success: true }
      } catch (error) {
        return { 
          success: false, 
          message: error.response?.data?.message || 'Login failed' 
        }
      } finally {
        this.loading = false
      }
    },
    
    async fetchUser() {
      try {
        const { data } = await axios.get('/api/auth/user')
        this.user = data.data
      } catch (error) {
        this.logout()
      }
    },
    
    async logout() {
      try {
        await axios.post('/api/auth/logout')
      } catch (error) {
        // Ignore logout errors
      }
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },
    
    setToken(token) {
      this.token = token
      localStorage.setItem('token', token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    },
    
    clearToken() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },

    async register(formData) {
      this.loading = true
      try {
        const { data } = await axios.post('/api/auth/register', formData)
        this.token = data.data.token
        localStorage.setItem('token', data.data.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        await this.fetchUser()
        return { success: true }
      } catch (error) {
        return { 
          success: false, 
          message: error.response?.data?.message || 'Registration failed',
          errors: error.response?.data?.errors
        }
      } finally {
        this.loading = false
      }
    }
  }
})

// Initialize axios headers if token exists
if (localStorage.getItem('token')) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`
}