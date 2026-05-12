<script setup>
import { ref, computed } from 'vue'
import { TransitionRoot, TransitionChild } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import GlobalButton from './GlobalButton.vue'

const props = defineProps({
  modelValue: Boolean,
  title: String,
  message: String,
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  confirmColor: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'success', 'warning', 'danger', 'info'].includes(v)
  },
  size: {
    type: String,
    default: 'sm',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  icon: String,
  loading: Boolean
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

const sizeClasses = {
  sm: 'max-w-sm',
  md: 'max-w-md',
  lg: 'max-w-lg'
}

const handleConfirm = () => {
  emit('confirm')
}

const handleCancel = () => {
  emit('update:modelValue', false)
  emit('cancel')
}
</script>

<template>
  <TransitionRoot appear :show="modelValue" as="template">
    <GlobalModal
      :modelValue="modelValue"
      :title="title"
      :size="size"
      :showClose="false"
      :closeable="false"
      @update:modelValue="$emit('update:modelValue', $event)"
    >
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
          <slot name="icon">
            <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </slot>
        </div>
        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
          {{ message }}
        </p>
      </div>
      
      <template #footer>
        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 justify-end">
          <GlobalButton
            variant="outline"
            :disabled="loading"
            @click="handleCancel"
          >
            {{ cancelText }}
          </GlobalButton>
          <GlobalButton
            :color="confirmColor"
            :loading="loading"
            @click="handleConfirm"
          >
            {{ confirmText }}
          </GlobalButton>
        </div>
      </template>
    </GlobalModal>
  </TransitionRoot>
</template>