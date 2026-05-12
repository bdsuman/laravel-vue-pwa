<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  label: String,
  disabled: Boolean,
  color: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'success', 'warning', 'danger', 'info'].includes(v)
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  id: String
})

const emit = defineEmits(['update:modelValue', 'change'])

const inputId = computed(() => props.id || `checkbox-${Math.random().toString(36).substr(2, 9)}`)

const colorClasses = {
  primary: 'bg-primary-600 border-primary-600 checked:bg-primary-600 checked:focus:bg-primary-600 focus:ring-primary-500',
  success: 'bg-green-600 border-green-600 checked:bg-green-600 checked:focus:bg-green-600 focus:ring-green-500',
  warning: 'bg-yellow-600 border-yellow-600 checked:bg-yellow-600 checked:focus:bg-yellow-600 focus:ring-yellow-500',
  danger: 'bg-red-600 border-red-600 checked:bg-red-600 checked:focus:bg-red-600 focus:ring-red-500',
  info: 'bg-blue-600 border-blue-600 checked:bg-blue-600 checked:focus:bg-blue-600 focus:ring-blue-500'
}

const sizeClasses = {
  sm: 'w-4 h-4',
  md: 'w-5 h-5',
  lg: 'w-6 h-6'
}

const handleChange = (e) => {
  if (!props.disabled) {
    emit('update:modelValue', e.target.checked)
    emit('change', e.target.checked)
  }
}
</script>

<template>
  <label 
    class="inline-flex items-center gap-2 cursor-pointer select-none"
    :class="{ 'opacity-50 cursor-not-allowed': disabled }"
  >
    <input
      :id="inputId"
      type="checkbox"
      :checked="modelValue"
      :disabled="disabled"
      @change="handleChange"
      class="rounded border-2 appearance-none cursor-pointer transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
      :class="[
        colorClasses[color],
        sizeClasses[size],
        disabled ? 'cursor-not-allowed' : 'cursor-pointer'
      ]"
    />
    <span 
      v-if="label" 
      class="text-sm font-medium text-gray-700 dark:text-gray-300"
      :class="{ 'pointer-events-none': disabled }"
    >
      {{ label }}
    </span>
  </label>
</template>