import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '../auth'
import axios from 'axios'

vi.mock('axios')

describe('useAuthStore', () => {
  beforeEach(() => {
    localStorage.clear()
    vi.clearAllMocks()
    setActivePinia(createPinia())
  })

  it('should initialize with no token', () => {
    const authStore = useAuthStore()
    expect(authStore.token).toBeNull()
    expect(authStore.isAuthenticated).toBe(false)
  })

  it('should login successfully', async () => {
    axios.post.mockResolvedValue({ data: { data: { token: 'test-token' } } })
    axios.get.mockResolvedValue({ data: { data: { name: 'Test User' } } })
    
    const authStore = useAuthStore()
    const result = await authStore.login('test@example.com', 'password')
    
    expect(result.success).toBe(true)
    expect(authStore.token).toBe('test-token')
    expect(authStore.isAuthenticated).toBe(true)
  })

  it('should handle login failure', async () => {
    axios.post.mockRejectedValue({ response: { data: { message: 'Invalid credentials' } } })
    
    const authStore = useAuthStore()
    const result = await authStore.login('test@example.com', 'wrong')
    
    expect(result.success).toBe(false)
    expect(result.message).toBe('Invalid credentials')
  })

  it('should logout and clear token', async () => {
    axios.post.mockResolvedValue({})
    
    const authStore = useAuthStore()
    authStore.token = 'test-token'
    authStore.user = { name: 'Test' }
    
    await authStore.logout()
    
    expect(authStore.token).toBeNull()
    expect(authStore.user).toBeNull()
    expect(localStorage.getItem('token')).toBeNull()
  })

  it('should set and clear token', () => {
    const authStore = useAuthStore()
    
    authStore.setToken('new-token')
    expect(authStore.token).toBe('new-token')
    expect(localStorage.getItem('token')).toBe('new-token')
    
    authStore.clearToken()
    expect(authStore.token).toBeNull()
  })
})
