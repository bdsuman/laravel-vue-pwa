<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold text-center mb-6 dark:text-white">Forgot Password</h2>
      <form @submit.prevent="submit">
        <GlobalInput
          v-model="form.email"
          label="Email Address"
          type="email"
          placeholder="Enter your email"
          :error="errors.email"
          required
          autocomplete="email"
        />
        <div v-if="successMessage" class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg">
          {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg">
          {{ errorMessage }}
        </div>
        <GlobalButton
          type="submit"
          :loading="loading"
          :disabled="loading"
          full-width
        >
          {{ loading ? 'Sending...' : 'Send OTP' }}
        </GlobalButton>
      </form>
      <p class="mt-4 text-center text-gray-600 dark:text-gray-400">
        Remember your password? 
        <router-link to="/login" class="text-primary-600 hover:underline">Login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCountdown } from '../composables/useForm'
import GlobalButton from '../components/GlobalButton.vue'
import GlobalInput from '../components/GlobalInput.vue'

const router = useRouter()
const form = ref({ email: '' })
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const { seconds, start: startCooldown, isActive } = useCountdown(0)

const submit = async () => {
  loading.value = true
  successMessage.value = ''
  errorMessage.value = ''
  
  try {
    const { data } = await axios.post('/api/auth/forgot-password', form.value)
    successMessage.value = data.message || 'OTP sent successfully'
    
    if (data.cooldown_remaining) {
      startCooldown(data.cooldown_remaining)
    }
    
    setTimeout(() => {
      router.push({ 
        name: 'verify-otp', 
        query: { email: form.value.email } 
      })
    }, 1500)
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Failed to send OTP'
    if (error.response?.data?.cooldown_remaining) {
      startCooldown(error.response.data.cooldown_remaining)
    }
  } finally {
    loading.value = false
  }
}
</script>
