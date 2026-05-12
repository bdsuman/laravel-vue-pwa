<script setup>
import { computed, ref } from 'vue'
import { ChevronDownIcon, CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: [String, Number, Array],
  options: {
    type: Array,
    required: true,
    default: () => []
  },
  label: String,
  placeholder: {
    type: String,
    default: 'Select an option'
  },
  error: String,
  hint: String,
  disabled: Boolean,
  multiple: Boolean,
  searchable: Boolean,
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  required: Boolean,
  id: String
})

const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const searchQuery = ref('')
const dropdownRef = ref(null)

const inputId = computed(() => props.id || `select-${Math.random().toString(36).substr(2, 9)}`)

const sizeClasses = computed(() => ({
  sm: 'py-1.5 px-3 text-sm',
  md: 'py-2 px-3 text-base',
  lg: 'py-3 px-4 text-lg'
}[props.size]))

const filteredOptions = computed(() => {
  if (!props.searchable || !searchQuery.value) return props.options
  const query = searchQuery.value.toLowerCase()
  return props.options.filter(opt => 
    (opt.label || opt).toString().toLowerCase().includes(query)
  )
})

const selectedLabel = computed(() => {
  if (props.multiple && Array.isArray(props.modelValue)) {
    if (props.modelValue.length === 0) return props.placeholder
    return `${props.modelValue.length} selected`
  }
  const selected = props.options.find(opt => 
    opt.value === props.modelValue || opt === props.modelValue
  )
  return selected ? (selected.label || selected) : props.placeholder
})

const isSelected = (option) => {
  const value = option.value ?? option
  if (props.multiple && Array.isArray(props.modelValue)) {
    return props.modelValue.includes(value)
  }
  return props.modelValue === value
}

const toggleOption = (option) => {
  if (props.disabled) return
  const value = option.value ?? option
  
  if (props.multiple) {
    const current = [...(props.modelValue || [])]
    const index = current.indexOf(value)
    if (index > -1) {
      current.splice(index, 1)
    } else {
      current.push(value)
    }
    emit('update:modelValue', current)
    emit('change', current)
  } else {
    emit('update:modelValue', value)
    emit('change', value)
    isOpen.value = false
  }
}

const handleClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false
    searchQuery.value = ''
  }
}

import { onMounted, onUnmounted, watch } from 'vue'

watch(isOpen, (open) => {
  if (open) {
    document.addEventListener('click', handleClickOutside)
  } else {
    document.removeEventListener('click', handleClickOutside)
    searchQuery.value = ''
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="w-full" ref="dropdownRef">
    <label 
      v-if="label" 
      :for="inputId" 
      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <div class="relative">
      <button
        :id="inputId"
        type="button"
        @click="disabled ? null : isOpen = !isOpen"
        :disabled="disabled"
        class="w-full flex items-center justify-between bg-white dark:bg-gray-700 border rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
        :class="[
          sizeClasses,
          error 
            ? 'border-red-500 dark:border-red-500 focus:ring-red-500' 
            : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500',
          disabled ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-60' : 'cursor-pointer'
        ]"
      >
        <span 
          class="flex-1 text-left truncate"
          :class="[
            selectedLabel === placeholder 
              ? 'text-gray-400 dark:text-gray-500' 
              : 'text-gray-900 dark:text-gray-100'
          ]"
        >
          {{ selectedLabel }}
        </span>
        <ChevronDownIcon 
          class="w-5 h-5 text-gray-400 ml-2 flex-shrink-0 transition-transform duration-200"
          :class="{ 'rotate-180': isOpen }"
        />
      </button>
      
      <Transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div 
          v-if="isOpen && !disabled"
          class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg overflow-hidden max-h-60 sm:max-h-80"
          style="top: 100%"
        >
          <div v-if="searchable" class="p-2 border-b border-gray-200 dark:border-gray-600">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search..."
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
              @click.stop
            />
          </div>
          
          <div class="overflow-y-auto max-h-48 sm:max-h-60">
            <button
              v-for="(option, index) in filteredOptions"
              :key="index"
              type="button"
              @click="toggleOption(option)"
              class="w-full px-4 py-2.5 text-left flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
              :class="{
                'bg-primary-50 dark:bg-primary-900/20': isSelected(option),
                'text-gray-900 dark:text-gray-100': !isSelected(option)
              }"
            >
              <span class="truncate">{{ option.label || option }}</span>
              <CheckIcon 
                v-if="isSelected(option)" 
                class="w-5 h-5 text-primary-600 flex-shrink-0"
              />
            </button>
            
            <div 
              v-if="filteredOptions.length === 0" 
              class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center text-sm"
            >
              No options found
            </div>
          </div>
        </div>
      </Transition>
    </div>
    
    <p v-if="error" class="mt-1.5 text-sm text-red-500">{{ error }}</p>
    <p v-else-if="hint" class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ hint }}</p>
  </div>
</template>
