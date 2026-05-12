<script setup>
import { computed } from 'vue'
import { 
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (v) => ['success', 'error', 'warning', 'info'].includes(v)
  },
  title: String,
  message: String,
  dismissible: {
    type: Boolean,
    default: true
  },
  icon: Boolean,
  variant: {
    type: String,
    default: 'filled',
    validator: (v) => ['filled', 'outline', 'soft'].includes(v)
  }
})

const emit = defineEmits(['close', 'dismiss'])

const alertConfig = {
  success: {
    classes: 'bg-green-50 dark:bg-green-900/30 border-green-500 text-green-800 dark:text-green-200',
    icon: CheckCircleIcon,
    iconColor: 'text-green-500'
  },
  error: {
    classes: 'bg-red-50 dark:bg-red-900/30 border-red-500 text-red-800 dark:text-red-200',
    icon: ExclamationCircleIcon,
    iconColor: 'text-red-500'
  },
  warning: {
    classes: 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-500 text-yellow-800 dark:text-yellow-200',
    icon: ExclamationTriangleIcon,
    iconColor: 'text-yellow-500'
  },
  info: {
    classes: 'bg-blue-50 dark:bg-blue-900/30 border-blue-500 text-blue-800 dark:text-blue-200',
    icon: InformationCircleIcon,
    iconColor: 'text-blue-500'
  }
}

const config = computed(() => alertConfig[props.type])
const AlertIcon = computed(() => config.value.icon)

const handleClose = () => {
  emit('close')
  emit('dismiss')
}
</script>

<template>
  <div
    class="w-full rounded-lg border p-3 sm:p-4 flex gap-2 sm:gap-3"
    :class="config.classes"
    role="alert"
  >
    <component 
      v-if="icon" 
      :is="AlertIcon" 
      class="h-5 w-5 shrink-0 mt-0.5" 
      :class="config.iconColor"
    />
    
    <div class="flex-1 min-w-0">
      <h4 v-if="title" class="font-semibold text-sm sm:text-base">
        {{ title }}
      </h4>
      <p class="text-sm mt-0.5">
        <slot>{{ message }}</slot>
      </p>
    </div>
    
    <button
      v-if="dismissible"
      @click="handleClose"
      type="button"
      class="shrink-0 p-1 rounded-md hover:bg-black/10 dark:hover:bg-white/10 transition-colors"
    >
      <XMarkIcon class="h-4 w-4 sm:h-5 sm:w-5 opacity-70" />
    </button>
  </div>
</template>