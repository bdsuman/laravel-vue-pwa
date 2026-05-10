<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold text-center mb-6">Register</h2>
      <form @submit.prevent="handleRegister">
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Name</label>
          <input v-model="form.name" type="text" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Email</label>
          <input v-model="form.email" type="email" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Password</label>
          <input v-model="form.password" type="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" />
        </div>
        <button type="submit" :disabled="loading" class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 disabled:opacity-50">
          {{ loading ? 'Loading...' : 'Register' }}
        </button>
      </form>
      <p class="mt-4 text-center text-gray-600">
        Already have an account? <router-link to="/login" class="text-primary-600 hover:underline">Login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const loading = ref(false)

const handleRegister = async () => {
  loading.value = true
  try {
    const { data } = await axios.post('/api/auth/register', form.value)
    localStorage.setItem('token', data.data.token)
    router.push('/')
  } catch (error) {
    alert(error.response?.data?.message || 'Registration failed')
  } finally {
    loading.value = false
  }
}
</script>
