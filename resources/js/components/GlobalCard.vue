<script setup>
import { computed } from 'vue'

const props = defineProps({
  padding: {
    type: String,
    default: 'md',
    validator: (v) => ['none', 'sm', 'md', 'lg'].includes(v)
  },
  shadow: {
    type: String,
    default: 'sm',
    validator: (v) => ['none', 'sm', 'md', 'lg'].includes(v)
  },
  rounded: {
    type: String,
    default: 'lg',
    validator: (v) => ['none', 'sm', 'md', 'lg', 'xl', 'full'].includes(v)
  },
  border: {
    type: Boolean,
    default: false
  },
  hoverable: {
    type: Boolean,
    default: false
  }
})

const cardClasses = computed(() => {
  const base = 'bg-white dark:bg-gray-800'
  
  const paddings = {
    none: '',
    sm: 'p-3',
    md: 'p-4 sm:p-6',
    lg: 'p-6 sm:p-8'
  }
  
  const shadows = {
    none: '',
    sm: 'shadow-sm',
    md: 'shadow-md',
    lg: 'shadow-lg'
  }
  
  const rounds = {
    none: 'rounded-none',
    sm: 'rounded',
    md: 'rounded-lg',
    lg: 'rounded-xl',
    xl: 'rounded-2xl',
    full: 'rounded-full'
  }
  
  const border = props.border ? 'border border-gray-200 dark:border-gray-700' : ''
  const hover = props.hoverable ? 'hover:shadow-lg transition-shadow duration-200 cursor-pointer' : ''
  
  return `${base} ${paddings[props.padding]} ${shadows[props.shadow]} ${rounds[props.rounded]} ${border} ${hover}`
})
</script>

<template>
  <div :class="cardClasses">
    <slot />
  </div>
</template>
