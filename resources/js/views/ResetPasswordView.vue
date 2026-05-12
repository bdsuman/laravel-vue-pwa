<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold text-center mb-6 dark:text-white">Reset Password</h2>
      <form @submit.prevent="submit">
        <GlobalInput
          v-model="form.email"
          label="Email"
          type="email"
          disabled
          :error="errors.email"
        />
        <GlobalInput
          v-model="form.password"
          label="New Password"
          type="password"
          placeholder="Enter new password"
          :error="errors.password"
          required
        />
        <GlobalInput
          v-model="form.password_confirmation"
          label="Confirm Password"
          type="password"
          placeholder="Confirm new password"
          :error="errors.password_confirmation"
          required
        />
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg">
          {{ successMessage }}
        </div>
        <GlobalButton
          type="submit"
          :loading="loading"
          :disabled="loading"
          full-width
        >
          Reset Password
        </GlobalButton>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import GlobalButton from '../components/GlobalButton.vue'
import GlobalInput from '../components/GlobalInput.vue'

const router = useRouter()
const route = useRoute()
const form = ref({ 
  email: '', 
  token: '', 
  password: '', 
  password_confirmation: '' 
})
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})

const submit = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match'
    return
  }
  
  loading.value = true
  errorMessage.value = ''
  errors.value = {}
  
  try {
    const { data } = await axios.post('/api/auth/reset-password', {
      email: form.value.email,
      token: form.value.token,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation
    })
    
    if (data.success) {
      successMessage.value = 'Password reset successfully!'
      setTimeout(() => {
        router.push('/login')
      }, 1500)
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to reset password'
    }
  } finally {
    loading.value = false
  }
}

// Set email and token from query params on mount
onMounted(() => {
  const email = route.query.email
  const token = route.query.token
  if (email && token) {
    form.value.email = email
    form.value.token = token
  } else {
    router.push('/forgot-password')
  }
})
</script>
