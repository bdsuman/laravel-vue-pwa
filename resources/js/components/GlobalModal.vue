<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import GlobalButton from './GlobalButton.vue'

const props = defineProps({
  modelValue: Boolean,
  title: String,
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg', 'xl', 'full'].includes(v)
  },
  closeable: {
    type: Boolean,
    default: true
  },
  persistent: Boolean,
  loading: Boolean,
  showClose: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue', 'close', 'cancel'])

const modalRef = ref(null)

const sizeClasses = {
  sm: 'max-w-sm',
  md: 'max-w-md',
  lg: 'max-w-lg',
  xl: 'max-w-xl',
  full: 'max-w-4xl'
}

const handleClose = () => {
  if (!props.loading) {
    emit('update:modelValue', false)
    emit('close')
  }
}

const handleCancel = () => {
  emit('cancel')
  handleClose()
}

const handleBackdropClick = (e) => {
  if (e.target === e.currentTarget && props.closeable && !props.persistent) {
    handleClose()
  }
}

const handleEscape = (e) => {
  if (e.key === 'Escape' && props.modelValue && props.closeable && !props.persistent && !props.loading) {
    handleClose()
  }
}

watch(() => props.modelValue, (val) => {
  if (val) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
  document.body.style.overflow = ''
})
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click="handleBackdropClick"
      >
        <Transition
          enter-active-class="transition-all duration-300"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="modelValue"
            ref="modalRef"
            class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-2xl flex flex-col max-h-[90vh]"
            :class="sizeClasses[size]"
          >
            <!-- Header -->
            <div v-if="title || showClose" class="flex items-center justify-between px-4 sm:px-6 py-4 border-b dark:border-gray-700">
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                {{ title }}
                <span v-if="loading" class="ml-2 inline-block animate-spin h-4 w-4 border-2 border-primary-500 border-t-transparent rounded-full"></span>
              </h3>
              <button
                v-if="showClose && closeable"
                @click="handleClose"
                type="button"
                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                :disabled="loading"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
            
            <!-- Body -->
            <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4">
              <slot />
            </div>
            
            <!-- Footer -->
            <div v-if="$slots.footer" class="px-4 sm:px-6 py-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-xl">
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>