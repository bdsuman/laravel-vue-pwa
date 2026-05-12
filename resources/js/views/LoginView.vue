<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
      <!-- Logo/Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome Back</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Sign in to your account</p>
      </div>

      <!-- Login Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Email Address
            </label>
            <GlobalInput
              v-model="form.email"
              label="Email"
              type="email"
              placeholder="Enter your email"
              :error="v$.form.email.$errors.length ? v$.form.email.$errors[0].$message : ''"
              autocomplete="email"
              :disabled="isLoading"
            />
          </div>

          <!-- Password Field -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Password
            </label>
            <GlobalInput
              v-model="form.password"
              label="Password"
              type="password"
              placeholder="Enter your password"
              :error="v$.form.password.$errors.length ? v$.form.password.$errors[0].$message : ''"
              autocomplete="current-password"
              :disabled="isLoading"
            />
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="form.remember"
                class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 dark:border-gray-600"
              />
              <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
            </label>
            <router-link
              to="/forgot-password"
              class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400"
            >
              Forgot password?
            </router-link>
          </div>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
          </div>

          <!-- Submit Button -->
          <GlobalButton
            type="submit"
            variant="primary"
            :loading="isLoading"
            :disabled="isLoading"
            class="w-full"
          >
            {{ isLoading ? 'Signing in...' : 'Sign In' }}
          </GlobalButton>

          <!-- Register Link -->
          <p class="text-center text-sm text-gray-600 dark:text-gray-400">
            Don't have an account?
            <router-link
              to="/register"
              class="text-primary-600 hover:text-primary-500 font-medium dark:text-primary-400"
            >
              Sign up
            </router-link>
          </p>
        </form>
      </div>

      <!-- Demo Credentials -->
      <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2">Demo Credentials</h4>
        <p class="text-xs text-blue-700 dark:text-blue-400">
          Email: demo@example.com | Password: password
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { required, email, minLength } from '@vuelidate/validators'
import { useAuthStore } from '../stores/auth'
import { useToast } from '../composables/useToast'
import GlobalButton from '../components/GlobalButton.vue'
import GlobalInput from '../components/GlobalInput.vue'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

// Form state
const form = ref({
  email: '',
  password: '',
  remember: false
})

// Error message
const errorMessage = ref('')

// Loading state
const isLoading = ref(false)

// Validation rules
const rules = {
  form: {
    email: {
      required,
      email,
      customEmail: (value) => {
        if (!value) return true
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        return emailRegex.test(value) || 'Please enter a valid email address'
      }
    },
    password: {
      required,
      minLength: minLength(6)
    }
  }
}

// Vuelidate instance
const v$ = useVuelidate(rules, form)

// Custom validation messages
const validationMessages = {
  'form.email.required': 'Email is required',
  'form.email.email': 'Please enter a valid email address',
  'form.password.required': 'Password is required',
  'form.password.minLength': 'Password must be at least 6 characters'
}

// Submit handler
const handleSubmit = async () => {
  errorMessage.value = ''
  
  // Validate form
  const isValid = await v$.value.$validate()
  
  if (!isValid) {
    const firstError = v$.value.form.$errors[0]
    if (firstError) {
      errorMessage.value = firstError.$message
    }
    return
  }
  
  isLoading.value = true
  
  try {
    const result = await authStore.login(form.value.email, form.value.password)
    
    if (result.success) {
      toast.success('Login successful! Redirecting to dashboard...')
      router.push('/dashboard')
    } else {
      errorMessage.value = result.message || 'Invalid credentials'
      toast.error(result.message || 'Login failed')
    }
  } catch (error) {
    const message = error.response?.data?.message || 'An error occurred. Please try again.'
    errorMessage.value = message
    toast.error(message)
  } finally {
    isLoading.value = false
  }
}
</script>
