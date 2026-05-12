<script setup>
const props = defineProps({
  text: String,
  color: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'success', 'warning', 'danger', 'info', 'gray'].includes(v)
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  variant: {
    type: String,
    default: 'solid',
    validator: (v) => ['solid', 'soft', 'outline'].includes(v)
  },
  pill: Boolean,
  removable: Boolean,
  icon: String
})

const emit = defineEmits(['click', 'remove'])

const colorVariantClasses = {
  solid: {
    primary: 'bg-primary-600 text-white',
    success: 'bg-green-600 text-white',
    warning: 'bg-yellow-600 text-white',
    danger: 'bg-red-600 text-white',
    info: 'bg-blue-600 text-white',
    gray: 'bg-gray-600 text-white'
  },
  soft: {
    primary: 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300',
    success: 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
    warning: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300',
    danger: 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
    info: 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
    gray: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
  },
  outline: {
    primary: 'border border-primary-500 text-primary-600 dark:text-primary-400',
    success: 'border border-green-500 text-green-600 dark:text-green-400',
    warning: 'border border-yellow-500 text-yellow-600 dark:text-yellow-400',
    danger: 'border border-red-500 text-red-600 dark:text-red-400',
    info: 'border border-blue-500 text-blue-600 dark:text-blue-400',
    gray: 'border border-gray-500 text-gray-600 dark:text-gray-400'
  }
}

const sizeClasses = {
  sm: 'px-2 py-0.5 text-xs',
  md: 'px-2.5 py-1 text-xs',
  lg: 'px-3 py-1.5 text-sm'
}
</script>

<template>
  <span
    class="inline-flex items-center gap-1 font-medium transition-colors"
    :class="[
      colorVariantClasses[variant][color],
      sizeClasses[size],
      pill ? 'rounded-full' : 'rounded'
    ]"
  >
    <span v-if="icon" v-html="icon" class="w-3 h-3" />
    <slot>{{ text }}</slot>
    <button
      v-if="removable"
      @click.stop="$emit('remove')"
      type="button"
      class="ml-1 rounded-full hover:bg-black/10 dark:hover:bg-white/10 transition-colors p-0.5"
    >
      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </span>
</template>