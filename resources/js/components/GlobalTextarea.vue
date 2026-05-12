<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: String,
  label: String,
  placeholder: String,
  error: String,
  hint: String,
  disabled: Boolean,
  rows: {
    type: [Number, String],
    default: 4
  },
  maxlength: [Number, String],
  showCount: Boolean,
  resize: {
    type: Boolean,
    default: true
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  required: Boolean,
  id: String
})

const emit = defineEmits(['update:modelValue', 'input', 'blur'])

const inputId = computed(() => props.id || `textarea-${Math.random().toString(36).substr(2, 9)}`)

const sizeClasses = computed(() => ({
  sm: 'py-2 px-3 text-sm',
  md: 'py-3 px-4 text-base',
  lg: 'py-4 px-5 text-lg'
}[props.size]))

const charCount = computed(() => (props.modelValue || '').length)
</script>

<template>
  <div class="w-full">
    <label 
      v-if="label" 
      :for="inputId" 
      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <textarea
      :id="inputId"
      :value="modelValue"
      @input="emit('update:modelValue', $event.target.value)"
      @blur="emit('blur', $event)"
      :placeholder="placeholder"
      :rows="rows"
      :maxlength="maxlength"
      :disabled="disabled"
      :required="required"
      class="w-full border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y dark:bg-gray-700 dark:text-gray-100"
      :class="[
        sizeClasses,
        error 
          ? 'border-red-500 dark:border-red-500 focus:ring-red-500' 
          : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:border-primary-500',
        disabled ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-60 resize-none' : '',
        resize ? 'resize' : 'resize-none'
      ]"
    />
    
    <div class="flex justify-between items-center mt-1">
      <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
      <p v-else-if="hint" class="text-sm text-gray-500 dark:text-gray-400">{{ hint }}</p>
      <span v-else></span>
      <span 
        v-if="showCount && maxlength" 
        class="text-xs text-gray-400"
        :class="{ 'text-red-500': charCount >= maxlength }"
      >
        {{ charCount }} / {{ maxlength }}
      </span>
    </div>
  </div>
</template>