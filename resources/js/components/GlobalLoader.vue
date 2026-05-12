<script setup>
import { computed } from 'vue'

const props = defineProps({
  fullScreen: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  color: {
    type: String,
    default: 'primary' // primary, white, neutral
  }
})

const sizeClasses = computed(() => ({
  sm: 'w-5 h-5 border-2',
  md: 'w-8 h-8 border-2',
  lg: 'w-12 h-12 border-3'
}[props.size]))

const colorClasses = computed(() => ({
  primary: 'border-t-primary-600 border-b-primary-600 border-l-transparent border-r-transparent',
  white: 'border-t-white border-b-white border-l-transparent border-r-transparent',
  neutral: 'border-t-gray-600 border-b-gray-600 border-l-transparent border-r-transparent'
}[props.color]))
</script>

<template>
  <div 
    class="inline-block"
    :class="{ 'fixed inset-0 flex items-center justify-center bg-black/20 z-50': fullScreen }"
  >
    <div 
      class="rounded-full animate-spin"
      :class="[sizeClasses, colorClasses]"
    ></div>
  </div>
</template>
