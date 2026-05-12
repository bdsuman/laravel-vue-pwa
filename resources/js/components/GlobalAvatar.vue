<script setup>
import { computed } from 'vue'

const props = defineProps({
  src: String,
  name: String,
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v)
  },
  shape: {
    type: String,
    default: 'circle',
    validator: (v) => ['circle', 'square', 'rounded'].includes(v)
  },
  status: String,
  statusPosition: {
    type: String,
    default: 'bottom-right'
  }
})

const initials = computed(() => {
  if (!props.name) return '?'
  return props.name
    .split(' ')
    .map(n => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
})

const sizeClasses = {
  xs: 'w-6 h-6 text-xs',
  sm: 'w-8 h-8 text-xs',
  md: 'w-10 h-10 text-sm',
  lg: 'w-12 h-12 text-base',
  xl: 'w-16 h-16 text-lg'
}

const statusSizeClasses = {
  xs: 'w-1.5 h-1.5 border',
  sm: 'w-2 h-2 border',
  md: 'w-2.5 h-2.5 border-2',
  lg: 'w-3 h-3 border-2',
  xl: 'w-4 h-4 border-2'
}

const statusColors = {
  online: 'bg-green-500',
  offline: 'bg-gray-400',
  busy: 'bg-red-500',
  away: 'bg-yellow-500'
}

const shapeClasses = computed(() => ({
  circle: 'rounded-full',
  square: 'rounded-none',
  rounded: 'rounded-lg'
}[props.shape]))
</script>

<template>
  <div class="relative inline-block">
    <div
      class="flex items-center justify-center overflow-hidden bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-semibold"
      :class="[sizeClasses[size], shapeClasses]"
    >
      <img
        v-if="src"
        :src="src"
        :alt="name"
        class="w-full h-full object-cover"
        @error="$event.target.style.display = 'none'"
      />
      <span v-else>{{ initials }}</span>
    </div>
    
    <!-- Status Indicator -->
    <span
      v-if="status"
      class="absolute border-white dark:border-gray-800"
      :class="[
        statusSizeClasses[size],
        statusColors[status] || 'bg-gray-500',
        statusPosition === 'bottom-right' ? '-bottom-0.5 -right-0.5' : '',
        statusPosition === 'top-right' ? '-top-0.5 -right-0.5' : '',
        statusPosition === 'bottom-left' ? '-bottom-0.5 -left-0.5' : '',
        statusPosition === 'top-left' ? '-top-0.5 -left-0.5' : '',
        shapeClasses
      ]"
    />
  </div>
</template>