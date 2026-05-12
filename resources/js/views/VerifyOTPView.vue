<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold text-center mb-4 dark:text-white">Verify OTP</h2>
      <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
        Enter the 6-digit code sent to your email
      </p>
      
      <!-- OTP Expiry Timer -->
      <div v-if="otpExpiry > 0" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-600 dark:text-gray-300">OTP Expires in:</span>
          <span 
            class="font-mono font-bold"
            :class="otpExpiry <= 60 ? 'text-red-500' : 'text-primary-600'"
          >
            {{ otpFormatted }}
          </span>
        </div>
        <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
          <div 
            class="h-2 rounded-full transition-all duration-1000"
            :class="otpExpiry <= 60 ? 'bg-red-500' : 'bg-primary-600'"
            :style="{ width: `${(otpExpiry / 600) * 100}%` }"
          ></div>
        </div>
      </div>
      
      <form @submit.prevent="handleSubmit">
        <GlobalInput
          v-model="form.email"
          label="Email"
          type="email"
          disabled
          :error="errors.email"
        />
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            OTP Code
          </label>
          <input 
            v-model="form.otp" 
            type="text" 
            required 
            maxlength="6"
            pattern="[0-9]{6}"
            placeholder="000000"
            class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-center text-2xl tracking-[1em] font-mono bg-white dark:bg-gray-700 dark:text-white"
          />
          <p v-if="errors.otp" class="mt-1 text-sm text-red-500">{{ errors.otp }}</p>
        </div>
        
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg">
          {{ errorMessage }}
        </div>
        
        <div class="mb-6 flex items-center justify-between">
          <button 
            type="button" 
            @click="resendOtp"
            :disabled="resendCooldown > 0 || loading"
            class="text-sm text-primary-600 hover:underline disabled:text-gray-400"
          >
            {{ resendCooldown > 0 ? `Resend in ${resendCooldown}s` : 'Resend OTP' }}
          </button>
          <span v-if="attemptsRemaining !== null" class="text-xs text-gray-500">
            {{ attemptsRemaining }} attempts left
          </span>
        </div>
        
        <GlobalButton
          type="submit"
          :loading="loading"
          :disabled="loading || form.otp.length !== 6"
          full-width
        >
          Verify OTP
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import GlobalButton from '../components/GlobalButton.vue'
import GlobalInput from '../components/GlobalInput.vue'

const router = useRouter()
const route = useRoute()

const form = ref({ email: '', otp: '' })
const errors = ref({})
const loading = ref(false)
const errorMessage = ref('')
const resendCooldown = ref(0)
const otpExpiry = ref(0)
const attemptsRemaining = ref(null)

let resendInterval = null
let expiryInterval = null

const otpFormatted = computed(() => {
  const mins = Math.floor(otpExpiry.value / 60)
  const secs = otpExpiry.value % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
})

const resendOtp = async () => {
  if (resendCooldown.value > 0) return
  
  try {
    const { data } = await axios.post('/api/auth/forgot-password', { email: form.value.email })
    errorMessage.value = ''
    resendCooldown.value = data.cooldown || 60
    otpExpiry.value = data.expires_in || 600
    startResendCountdown()
    startExpiryCountdown()
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Failed to resend OTP'
    if (error.response?.data?.cooldown_remaining) {
      resendCooldown.value = error.response.data.cooldown_remaining
      startResendCountdown()
    }
  }
}

const startResendCountdown = () => {
  if (resendInterval) clearInterval(resendInterval)
  resendInterval = setInterval(() => {
    if (resendCooldown.value > 0) {
      resendCooldown.value--
    } else {
      clearInterval(resendInterval)
    }
  }, 1000)
}

const startExpiryCountdown = () => {
  if (expiryInterval) clearInterval(expiryInterval)
  expiryInterval = setInterval(() => {
    if (otpExpiry.value > 0) {
      otpExpiry.value--
    } else {
      clearInterval(expiryInterval)
    }
  }, 1000)
}

const handleSubmit = async () => {
  if (form.value.otp.length !== 6) {
    errors.value.otp = 'Please enter a 6-digit OTP'
    return
  }
  
  loading.value = true
  errors.value = {}
  errorMessage.value = ''
  
  try {
    const { data } = await axios.post('/api/auth/verify-otp', {
      email: form.value.email,
      otp: form.value.otp
    })
    
    if (data.success) {
      router.push({ 
        name: 'reset-password', 
        query: { 
          email: form.value.email, 
          token: data.token 
        } 
      })
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errorMessage.value = error.response?.data?.message || 'Invalid OTP'
    }
    if (error.response?.data?.attempts_remaining) {
      attemptsRemaining.value = error.response.data.attempts_remaining
    }
    if (error.response?.data?.lock_remaining) {
      resendCooldown.value = error.response.data.lock_remaining
      startResendCountdown()
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  form.value.email = route.query.email || ''
  if (!form.value.email) {
    router.push('/forgot-password')
  } else {
    // Initialize timers if coming back from forgot-password
    otpExpiry.value = route.query.expires_in ? parseInt(route.query.expires_in) : 600
    startExpiryCountdown()
  }
})

onUnmounted(() => {
  if (resendInterval) clearInterval(resendInterval)
  if (expiryInterval) clearInterval(expiryInterval)
})
</script>
