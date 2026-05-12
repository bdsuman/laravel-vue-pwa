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
  removable: Boolean,
  closable: Boolean
})

const emit = defineEmits(['click', 'remove'])

const colorClasses = {
  primary: 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300',
  success: 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
  warning: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300',
  danger: 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
  info: 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
  gray: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
}

const sizeClasses = {
  sm: 'px-2 py-0.5 text-xs',
  md: 'px-2.5 py-1 text-sm',
  lg: 'px-3 py-1.5 text-base'
}
</script>

<template>
  <span
    @click="$emit('click')"
    class="inline-flex items-center gap-1 rounded-full font-medium transition-colors"
    :class="[
      colorClasses[color],
      sizeClasses[size],
      $attrs.onClick ? 'cursor-pointer hover:opacity-80' : ''
    ]"
  >
    <slot name="icon-left" />
    <span class="truncate max-w-[150px]">
      <slot>{{ text }}</slot>
    </span>
    <slot name="icon-right" />
    
    <button
      v-if="removable || closable"
      @click.stop="$emit('remove')"
      type="button"
      class="ml-0.5 rounded-full hover:bg-black/10 dark:hover:bg-white/10 transition-colors p-0.5"
    >
      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </span>
</template>