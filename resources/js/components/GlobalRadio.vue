<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: [String, Number],
  options: {
    type: Array,
    required: true,
    default: () => []
  },
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
  horizontal: Boolean,
  id: String
})

const emit = defineEmits(['update:modelValue', 'change'])

const inputId = computed(() => props.id || `radio-${Math.random().toString(36).substr(2, 9)}`)

const colorClasses = {
  primary: 'border-primary-600 checked:bg-primary-600 checked:border-primary-600 focus:ring-primary-500',
  success: 'border-green-600 checked:bg-green-600 checked:border-green-600 focus:ring-green-500',
  warning: 'border-yellow-600 checked:bg-yellow-600 checked:border-yellow-600 focus:ring-yellow-500',
  danger: 'border-red-600 checked:bg-red-600 checked:border-red-600 focus:ring-red-500',
  info: 'border-blue-600 checked:bg-blue-600 checked:border-blue-600 focus:ring-blue-500'
}

const sizeClasses = {
  sm: 'w-4 h-4',
  md: 'w-5 h-5',
  lg: 'w-6 h-6'
}

const dotSize = {
  sm: 'w-2 h-2',
  md: 'w-2.5 h-2.5',
  lg: 'w-3 h-3'
}

const handleChange = (value) => {
  if (!props.disabled) {
    emit('update:modelValue', value)
    emit('change', value)
  }
}
</script>

<template>
  <div class="w-full">
    <label 
      v-if="label" 
      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
    >
      {{ label }}
    </label>
    
    <div 
      class="flex flex-wrap gap-2 sm:gap-4"
      :class="{ 'flex-row': horizontal, 'flex-col': !horizontal }"
    >
      <label
        v-for="(option, index) in options"
        :key="index"
        class="inline-flex items-center gap-2 cursor-pointer select-none"
        :class="{ 'opacity-50 cursor-not-allowed': disabled }"
      >
        <input
          type="radio"
          :name="inputId"
          :value="option.value ?? option"
          :checked="modelValue === (option.value ?? option)"
          :disabled="disabled"
          @change="handleChange(option.value ?? option)"
          class="rounded-full border-2 appearance-none cursor-pointer transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900 relative"
          :class="[
            colorClasses[color],
            sizeClasses[size],
            disabled ? 'cursor-not-allowed' : 'cursor-pointer'
          ]"
        />
        <span 
          class="absolute inset-0 m-auto rounded-full"
          :class="[
            dotSize[size],
            modelValue === (option.value ?? option) ? 'bg-white' : 'bg-transparent'
          ]"
          style="pointer-events: none"
        />
        <span 
          class="text-sm font-medium text-gray-700 dark:text-gray-300"
          :class="{ 'pointer-events-none': disabled }"
        >
          {{ option.label || option }}
        </span>
      </label>
    </div>
  </div>
</template>