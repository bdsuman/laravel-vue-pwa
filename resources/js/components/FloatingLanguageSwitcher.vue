<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { setLocale } from '../i18n'

const { locale } = useI18n()

const languages = [
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'bn', name: 'বাংলা', flag: '🇧🇩' },
  { code: 'hi', name: 'हिंदी', flag: '🇮🇳' }
]

const isOpen = ref(false)
const dropdownRef = ref(null)

const currentLang = computed(() => {
  return languages.find(l => l.code === locale.value) || languages[0]
})

function toggleDropdown() {
  isOpen.value = !isOpen.value
}

function changeLocale(code) {
  setLocale(code)
  isOpen.value = false
}

function handleClickOutside(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div ref="dropdownRef" class="fixed bottom-6 right-6 z-50">
    <!-- Main Button -->
    <button
      @click="toggleDropdown"
      class="w-14 h-14 rounded-full bg-primary-600 hover:bg-primary-700 shadow-lg flex items-center justify-center transition-all hover:scale-105"
      :title="'Language: ' + currentLang.name"
    >
      <span class="text-2xl">{{ currentLang.flag }}</span>
    </button>

    <!-- Dropdown -->
    <Transition name="float">
      <div 
        v-if="isOpen" 
        class="absolute bottom-full right-0 mb-3 py-2 w-44 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700"
      >
        <button
          v-for="lang in languages"
          :key="lang.code"
          @click="changeLocale(lang.code)"
          class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          :class="{ 'bg-primary-50 dark:bg-primary-900/30 text-primary-600': lang.code === locale }"
        >
          <span class="text-lg">{{ lang.flag }}</span>
          <span class="font-medium">{{ lang.name }}</span>
        </button>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.float-enter-active,
.float-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.float-enter-from,
.float-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.95);
}
</style>
