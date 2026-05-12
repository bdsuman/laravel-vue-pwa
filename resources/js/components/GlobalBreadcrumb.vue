<script setup>
import { computed } from 'vue'
import { HomeIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  separator: {
    type: String,
    default: '›',
    validator: (v) => ['›', '/', '>', '→'].includes(v)
  },
  separatorIcon: Boolean,
  homeIcon: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['click'])

const handleClick = (item, index) => {
  if (!item.disabled) {
    emit('click', item, index)
  }
}
</script>

<template>
  <nav class="flex items-center flex-wrap" aria-label="Breadcrumb">
    <ol class="flex items-center flex-wrap gap-1 sm:gap-2">
      <!-- Home -->
      <li v-if="homeIcon">
        <button
          type="button"
          @click="handleClick({ label: 'Home', href: '/' }, 0)"
          class="inline-flex items-center text-sm text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors"
        >
          <HomeIcon class="h-4 w-4" />
        </button>
      </li>
      
      <!-- Separator after home -->
      <li v-if="homeIcon" class="flex items-center text-gray-400 dark:text-gray-500">
        <ChevronRightIcon v-if="separatorIcon" class="h-4 w-4" />
        <span v-else class="text-lg">{{ separator }}</span>
      </li>
      
      <!-- Breadcrumb Items -->
      <li 
        v-for="(item, index) in items" 
        :key="index"
        class="flex items-center gap-1 sm:gap-2"
      >
        <button
          type="button"
          @click="handleClick(item, index)"
          :disabled="item.disabled || index === items.length - 1"
          class="text-sm truncate max-w-[150px] sm:max-w-[200px] transition-colors"
          :class="[
            index === items.length - 1 || item.disabled
              ? 'text-gray-700 dark:text-gray-300 font-medium cursor-default' 
              : 'text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 hover:underline'
          ]"
        >
          {{ item.label || item }}
        </button>
        
        <!-- Separator -->
        <ChevronRightIcon v-if="separatorIcon && index < items.length - 1" class="h-4 w-4 text-gray-400 shrink-0" />
        <span v-else-if="index < items.length - 1" class="text-lg text-gray-400">{{ separator }}</span>
      </li>
    </ol>
  </nav>
</template>