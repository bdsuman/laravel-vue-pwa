<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { setLocale } from '../i18n'

const { locale, t } = useI18n()

const languages = [
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'bn', name: 'বাংলা', flag: '🇧🇩' },
  { code: 'hi', name: 'हिंदी', flag: '🇮🇳' }
]

const isOpen = ref(false)

const currentLang = computed(() => {
  return languages.find(l => l.code === locale.value) || languages[0]
})

function changeLocale(code) {
  setLocale(code)
  isOpen.value = false
}

function toggleDropdown() {
  isOpen.value = !isOpen.value
}

// Close on outside click
function handleClickOutside(event) {
  if (!event.target.closest('.language-switcher')) {
    isOpen.value = false
  }
}
</script>

<template>
  <div class="language-switcher relative" @click="toggleDropdown">
    <button 
      class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors"
      :title="t('common.settings')"
    >
      <span class="text-lg">{{ currentLang.flag }}</span>
      <span class="text-sm font-medium">{{ currentLang.code.toUpperCase() }}</span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <Transition name="dropdown">
      <div 
        v-if="isOpen" 
        class="absolute right-0 mt-2 py-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
      >
        <button
          v-for="lang in languages"
          :key="lang.code"
          @click.stop="changeLocale(lang.code)"
          class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          :class="{ 'bg-gray-100 dark:bg-gray-700': lang.code === locale }"
        >
          <span class="text-lg">{{ lang.flag }}</span>
          <span>{{ lang.name }}</span>
          <svg 
            v-if="lang.code === locale" 
            class="w-4 h-4 ml-auto text-blue-600" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </button>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
