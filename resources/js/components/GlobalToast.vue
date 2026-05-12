<script setup>
import { TransitionGroup } from 'vue'
import { toasts } from '../composables/useToast.js'

const removeToast = (id) => {
  const index = toasts.value.findIndex(t => t.id === id)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}
</script>

<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[100] flex flex-col gap-2 w-full max-w-sm sm:max-w-md">
      <TransitionGroup
        enter-active-class="transition-all duration-300"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition-all duration-200"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="flex items-start gap-3 p-3 sm:p-4 rounded-lg border-l-4 shadow-xl backdrop-blur-sm"
          :class="toast.colorClass"
        >
          <span v-html="toast.icon" class="shrink-0 mt-0.5" />
          
          <div class="flex-1 min-w-0">
            <h5 v-if="toast.title" class="font-semibold text-sm mb-0.5">{{ toast.title }}</h5>
            <p class="text-sm">{{ toast.message }}</p>
          </div>
          
          <button
            @click="removeToast(toast.id)"
            class="shrink-0 p-1 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors"
          >
            <svg class="h-4 w-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>