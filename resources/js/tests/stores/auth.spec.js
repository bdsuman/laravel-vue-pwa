import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'

// Mock localStorage
const localStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn()
}
global.localStorage = localStorageMock

describe('Auth Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
    vi.clearAllMocks()
  })

  it('should have initial state with no token', () => {
    const store = useAuthStore()
    
    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })

  it('should set token and update isAuthenticated', () => {
    const store = useAuthStore()
    
    store.setToken('test-token-123')
    
    expect(store.token).toBe('test-token-123')
    expect(store.isAuthenticated).toBe(true)
    expect(localStorage.setItem).toHaveBeenCalledWith('token', 'test-token-123')
    expect(axios.defaults.headers.common['Authorization']).toBe('Bearer test-token-123')
  })

  it('should clear token and reset state', () => {
    const store = useAuthStore()
    
    store.setToken('test-token')
    store.clearToken()
    
    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(localStorage.removeItem).toHaveBeenCalledWith('token')
  })

  it('should login successfully', async () => {
    const store = useAuthStore()
    
    const mockResponse = {
      data: {
        success: true,
        data: {
          token: 'auth-token-xyz',
          user: { id: 1, name: 'Test User', email: 'test@example.com' }
        }
      }
    }
    
    axios.post = vi.fn().mockResolvedValue(mockResponse)
    axios.get = vi.fn().mockResolvedValue({ data: { data: { id: 1, name: 'Test User', email: 'test@example.com' } } })
    
    const result = await store.login('test@example.com', 'password123')
    
    expect(result.success).toBe(true)
    expect(store.token).toBe('auth-token-xyz')
    expect(store.user).toBeTruthy()
  })

  it('should handle login failure', async () => {
    const store = useAuthStore()
    
    axios.post = vi.fn().mockRejectedValue({
      response: { data: { message: 'Invalid credentials' } }
    })
    
    const result = await store.login('wrong@example.com', 'wrongpassword')
    
    expect(result.success).toBe(false)
    expect(result.message).toBe('Invalid credentials')
    expect(store.token).toBeNull()
  })

  it('should logout and clear everything', async () => {
    const store = useAuthStore()
    
    axios.post = vi.fn().mockResolvedValue({})
    
    store.setToken('test-token')
    await store.logout()
    
    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(localStorage.removeItem).toHaveBeenCalledWith('token')
  })

  it('should register successfully', async () => {
    const store = useAuthStore()
    
    const mockResponse = {
      data: {
        success: true,
        data: {
          token: 'register-token-xyz',
          user: { id: 2, name: 'New User', email: 'new@example.com' }
        }
      }
    }
    
    axios.post = vi.fn().mockResolvedValue(mockResponse)
    axios.get = vi.fn().mockResolvedValue({ data: { data: { id: 2, name: 'New User', email: 'new@example.com' } } })
    
    const result = await store.register({ name: 'New User', email: 'new@example.com', password: 'password123' })
    
    expect(result.success).toBe(true)
    expect(store.token).toBe('register-token-xyz')
  })
})