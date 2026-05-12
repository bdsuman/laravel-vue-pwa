<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 100
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  color: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'success', 'warning', 'danger', 'info'].includes(v)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'thin', 'thick', 'labeled'].includes(v)
  },
  showValue: Boolean,
  showPercentage: Boolean,
  striped: Boolean,
  animated: Boolean,
  label: String
})

const percentage = computed(() => {
  return Math.round((props.value / props.max) * 100)
})

const barColor = {
  primary: 'bg-primary-600',
  success: 'bg-green-600',
  warning: 'bg-yellow-600',
  danger: 'bg-red-600',
  info: 'bg-blue-600'
}

const sizeClasses = {
  sm: 'h-1',
  md: 'h-2',
  lg: 'h-3'
}

const trackClasses = {
  sm: 'h-1',
  md: 'h-2',
  lg: 'h-3',
  default: 'h-2',
  thin: 'h-1',
  thick: 'h-3'
}
</script>

<template>
  <div class="w-full">
    <!-- Label -->
    <div v-if="label || showValue || showPercentage" class="flex justify-between items-center mb-1 text-sm">
      <span v-if="label" class="font-medium text-gray-700 dark:text-gray-300">{{ label }}</span>
      <span v-if="showValue || showPercentage" class="text-gray-500 dark:text-gray-400">
        {{ showPercentage ? `${percentage}%` : `${value}/${max}` }}
      </span>
    </div>
    
    <!-- Progress Bar -->
    <div 
      class="w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
      :class="trackClasses[variant] || 'h-2'"
    >
      <div
        class="h-full rounded-full transition-all duration-300 ease-out"
        :class="[
          barColor[color],
          sizeClasses[size],
          striped ? 'bg-stripes' : '',
          animated ? 'animate-pulse' : ''
        ]"
        :style="{ width: `${percentage}%` }"
      />
    </div>
  </div>
</template>

<style scoped>
.bg-stripes {
  background-image: linear-gradient(
    45deg,
    rgba(255, 255, 255, 0.15) 25%,
    transparent 25%,
    transparent 50%,
    rgba(255, 255, 255, 0.15) 50%,
    rgba(255, 255, 255, 0.15) 75%,
    transparent 75%,
    transparent
  );
  background-size: 1rem 1rem;
}
</style>