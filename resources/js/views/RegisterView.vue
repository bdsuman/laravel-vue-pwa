<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold text-center mb-6 dark:text-white">Register</h2>
      <form @submit.prevent="submit">
        <GlobalInput
          v-model="form.name"
          label="Name"
          type="text"
          placeholder="Enter your name"
          :error="errors.name"
          required
          autocomplete="name"
        />
        <GlobalInput
          v-model="form.email"
          label="Email"
          type="email"
          placeholder="Enter your email"
          :error="errors.email"
          required
          autocomplete="email"
        />
        <GlobalInput
          v-model="form.password"
          label="Password"
          type="password"
          placeholder="Enter your password"
          :error="errors.password"
          required
        />
        <GlobalInput
          v-model="form.password_confirmation"
          label="Confirm Password"
          type="password"
          placeholder="Confirm your password"
          :error="errors.password_confirmation"
          required
        />
        <GlobalButton
          type="submit"
          :loading="loading"
          :disabled="loading"
          full-width
        >
          Register
        </GlobalButton>
      </form>
      <p class="mt-4 text-center text-gray-600 dark:text-gray-400">
        Already have an account? 
        <router-link to="/login" class="text-primary-600 hover:underline">Login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import GlobalButton from '../components/GlobalButton.vue'
import GlobalInput from '../components/GlobalInput.vue'

const router = useRouter()
const authStore = useAuthStore()
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const loading = ref(false)
const errors = ref({})

const submit = async () => {
  loading.value = true
  errors.value = {}
  
  try {
    const result = await authStore.register(form.value)
    if (result.success) {
      router.push('/dashboard')
    } else {
      if (result.errors) {
        errors.value = result.errors
      } else {
        alert(result.message)
      }
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      alert(error.response?.data?.message || 'Registration failed')
    }
  } finally {
    loading.value = false
  }
}
</script>
