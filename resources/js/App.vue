<script setup>
import { RouterView, RouterLink } from 'vue-router'
import { useAuthStore } from './stores/auth'
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from './components/LanguageSwitcher.vue'
import { setLocale } from './i18n'

const { t, locale } = useI18n()
const authStore = useAuthStore()

const logout = async () => {
  await authStore.logout()
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav v-if="authStore.isAuthenticated" class="bg-white dark:bg-gray-800 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <RouterLink to="/dashboard" class="text-xl font-bold text-gray-900 dark:text-white">
              Laravel Vue PWA
            </RouterLink>
            <div class="hidden md:flex ml-10 space-x-4">
              <RouterLink 
                to="/dashboard" 
                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/dashboard' }"
              >
                {{ t('nav.dashboard') }}
              </RouterLink>
              <RouterLink 
                to="/posts" 
                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/posts' }"
              >
                {{ t('nav.posts') }}
              </RouterLink>
              <RouterLink 
                to="/categories" 
                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/categories' }"
              >
                {{ t('nav.categories') }}
              </RouterLink>
            </div>
          </div>
          
          <div class="flex items-center gap-4">
            <LanguageSwitcher />
            
            <div class="flex items-center gap-4">
              <span class="text-sm text-gray-600 dark:text-gray-300">
                {{ authStore.user?.name }}
              </span>
              <button
                @click="logout"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
              >
                {{ t('auth.logout') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <RouterView />
    </main>
  </div>
</template>
