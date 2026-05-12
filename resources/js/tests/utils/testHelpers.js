import axios from 'axios'
import { useAuthStore } from '../stores/auth'

// Track all API calls
let apiCalls = []

// Mock axios
jest.mock('axios', () => {
  const mockAxios = {
    defaults: {
      headers: {
        common: {}
      }
    },
    get: jest.fn(),
    post: jest.fn(),
    put: jest.fn(),
    patch: jest.fn(),
    delete: jest.fn(),
    create: jest.fn(() => mockAxios)
  }
  return mockAxios
})

beforeEach(() => {
  apiCalls = []
  localStorage.clear()
})

export function trackApiCall(method, url, data) {
  apiCalls.push({ method, url, data, timestamp: Date.now() })
  return { method, url, data }
}

export function getApiCalls() {
  return [...apiCalls]
}

export function clearApiCalls() {
  apiCalls = []
}

export function assertApiCalled(method, url) {
  const called = apiCalls.some(call => call.method === method && call.url === url)
  expect(called).toBe(true)
}

export function assertApiNotCalled(method, url) {
  const called = apiCalls.some(call => call.method === method && call.url === url)
  expect(called).toBe(false)
}

export function mockApiResponse(data, status = 200) {
  return Promise.resolve({ data, status })
}

export function mockApiError(message, status = 400) {
  return Promise.reject({ response: { data: { message }, status } })
}

// Auth store helpers
export function mockAuthStore() {
  const store = {
    token: null,
    user: null,
    loading: false,
    isAuthenticated: false,
    login: jest.fn(),
    logout: jest.fn(),
    register: jest.fn(),
    fetchUser: jest.fn(),
    setToken: jest.fn(),
    clearToken: jest.fn()
  }
  return store
}