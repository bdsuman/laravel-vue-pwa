<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  label: String,
  disabled: Boolean,
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
  id: String
})

const emit = defineEmits(['update:modelValue', 'change'])

const inputId = computed(() => props.id || `switch-${Math.random().toString(36).substr(2, 9)}`)

const trackColors = {
  off: 'bg-gray-200 dark:bg-gray-600',
  on: {
    primary: 'bg-primary-600',
    success: 'bg-green-600',
    warning: 'bg-yellow-600',
    danger: 'bg-red-600',
    info: 'bg-blue-600'
  }
}

const thumbColors = {
  on: {
    primary: 'bg-white',
    success: 'bg-white',
    warning: 'bg-white',
    danger: 'bg-white',
    info: 'bg-white'
  }
}

const sizeClasses = computed(() => ({
  sm: {
    track: 'w-8 h-4',
    thumb: 'w-3 h-3',
    translate: 'translate-x-4'
  },
  md: {
    track: 'w-11 h-6',
    thumb: 'w-5 h-5',
    translate: 'translate-x-5'
  },
  lg: {
    track: 'w-14 h-7',
    thumb: 'w-6 h-6',
    translate: 'translate-x-7'
  }
}[props.size]))

const handleChange = () => {
  if (!props.disabled) {
    const newValue = !props.modelValue
    emit('update:modelValue', newValue)
    emit('change', newValue)
  }
}
</script>

<template>
  <label 
    class="inline-flex items-center gap-3 cursor-pointer select-none"
    :class="{ 'opacity-50 cursor-not-allowed': disabled }"
  >
    <button
      type="button"
      role="switch"
      :aria-checked="modelValue"
      :aria-labelledby="inputId"
      :disabled="disabled"
      @click="handleChange"
      class="relative inline-flex items-center shrink-0 rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
      :class="[
        sizeClasses.track,
        disabled ? 'cursor-not-allowed' : 'cursor-pointer',
        modelValue ? trackColors.on[color] : trackColors.off
      ]"
    >
      <span 
        class="inline-block rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out"
        :class="[
          sizeClasses.thumb,
          modelValue ? sizeClasses.translate : 'translate-x-0.5'
        ]"
      />
    </button>
    
    <span 
      v-if="label" 
      class="text-sm font-medium text-gray-700 dark:text-gray-300"
      :id="inputId"
      :class="{ 'pointer-events-none': disabled }"
    >
      {{ label }}
    </span>
  </label>
</template>