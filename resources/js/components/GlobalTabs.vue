<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  tabs: {
    type: Array,
    required: true
  },
  modelValue: [String, Number],
  variant: {
    type: String,
    default: 'line',
    validator: (v) => ['line', 'pills', 'boxed', 'lift'].includes(v)
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  align: {
    type: String,
    default: 'left',
    validator: (v) => ['left', 'center', 'right', 'fill', 'justify'].includes(v)
  },
  scrollable: Boolean
})

const emit = defineEmits(['update:modelValue', 'change'])

const activeTab = computed({
  get: () => props.modelValue,
  set: (val) => {
    emit('update:modelValue', val)
    emit('change', val)
  }
})

const selectTab = (tab) => {
  activeTab.value = tab.value ?? tab
}

const sizeClasses = {
  sm: 'text-xs px-3 py-1.5',
  md: 'text-sm px-4 py-2',
  lg: 'text-base px-5 py-2.5'
}

const variantClasses = {
  line: {
    container: 'border-b border-gray-200 dark:border-gray-700',
    tab: 'border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300',
    activeTab: 'border-primary-500 text-primary-600 dark:text-primary-400'
  },
  pills: {
    container: '',
    tab: 'rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-gray-700',
    activeTab: 'bg-primary-600 text-white'
  },
  boxed: {
    container: 'border-b border-gray-200 dark:border-gray-700',
    tab: 'border border-b-0 rounded-t-lg text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:hover:text-gray-300 dark:hover:bg-gray-800',
    activeTab: 'bg-white dark:bg-gray-800 border-primary-500 text-primary-600 dark:text-primary-400'
  },
  lift: {
    container: '',
    tab: 'rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:hover:text-gray-300 dark:hover:bg-gray-800 shadow-sm',
    activeTab: 'bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-400 shadow-md'
  }
}

const alignClasses = {
  left: 'justify-start',
  center: 'justify-center',
  right: 'justify-end',
  fill: 'justify-start',
  justify: 'justify-between'
}
</script>

<template>
  <div class="w-full">
    <!-- Tab Container -->
    <div 
      class="flex flex-wrap"
      :class="[
        variantClasses[variant].container,
        alignClasses[align]
      ]"
    >
      <button
        v-for="(tab, index) in tabs"
        :key="index"
        type="button"
        @click="selectTab(tab)"
        class="inline-flex items-center justify-center gap-2 font-medium transition-all duration-200 whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 first:rounded-t-lg last:rounded-b-lg"
        :class="[
          sizeClasses[size],
          variantClasses[variant].tab,
          activeTab === (tab.value ?? tab) ? variantClasses[variant].activeTab : '',
          align === 'fill' ? 'flex-1' : '',
          align === 'justify' ? 'flex-1' : ''
        ]"
      >
        <span v-if="tab.icon" v-html="tab.icon" class="w-4 h-4" />
        {{ tab.label || tab }}
        <span 
          v-if="tab.badge" 
          class="ml-1 px-1.5 py-0.5 text-xs font-semibold rounded-full"
          :class="tab.badgeColor ? '' : 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-300'"
        >
          {{ tab.badge }}
        </span>
      </button>
    </div>
    
    <!-- Tab Content -->
    <div class="mt-4">
      <slot 
        v-for="(tab, index) in tabs" 
        :name="tab.value ?? tab" 
        :tab="tab"
      >
        <!-- Fallback content -->
      </slot>
    </div>
  </div>
</template>