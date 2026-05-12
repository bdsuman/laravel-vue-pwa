<script setup>
import { ref } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  text: String,
  items: {
    type: Array,
    default: () => []
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  color: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'primary', 'success', 'warning', 'danger', 'info'].includes(v)
  },
  variant: {
    type: String,
    default: 'outline',
    validator: (v) => ['solid', 'outline'].includes(v)
  },
  disabled: Boolean,
  split: Boolean,
  iconPosition: {
    type: String,
    default: 'right',
    validator: (v) => ['left', 'right'].includes(v)
  }
})

const emit = defineEmits(['click', 'select'])

const colorClasses = {
  default: {
    solid: 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200',
    outline: 'border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200'
  },
  primary: {
    solid: 'bg-primary-600 hover:bg-primary-700 text-white',
    outline: 'border border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-primary-600 dark:text-primary-400'
  },
  success: {
    solid: 'bg-green-600 hover:bg-green-700 text-white',
    outline: 'border border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 text-green-600 dark:text-green-400'
  },
  warning: {
    solid: 'bg-yellow-600 hover:bg-yellow-700 text-white',
    outline: 'border border-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400'
  },
  danger: {
    solid: 'bg-red-600 hover:bg-red-700 text-white',
    outline: 'border border-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400'
  },
  info: {
    solid: 'bg-blue-600 hover:bg-blue-700 text-white',
    outline: 'border border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400'
  }
}

const sizeClasses = {
  sm: 'px-3 py-1.5 text-sm',
  md: 'px-4 py-2 text-sm',
  lg: 'px-5 py-2.5 text-base'
}

const handleSelect = (item) => {
  emit('select', item)
}
</script>

<template>
  <Menu as="div" class="relative inline-block text-left">
    <div class="flex">
      <!-- Main Button -->
      <MenuButton
        as="template"
        :disabled="disabled"
        class="inline-flex items-center justify-center rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
        :class="[
          sizeClasses[size],
          colorClasses[color][variant],
          disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
          !split ? 'w-full' : '',
          split ? 'rounded-l-lg border-r-0' : 'rounded-lg'
        ]"
      >
        <slot name="icon-left">
          <span v-if="iconPosition === 'left'" class="mr-2">
            <slot name="icon" />
          </span>
        </slot>
        <slot>{{ text }}</slot>
        <slot name="icon-right">
          <span v-if="iconPosition === 'right'" class="ml-2">
            <ChevronDownIcon class="h-4 w-4" />
          </span>
        </slot>
      </MenuButton>
      
      <!-- Split Dropdown Arrow -->
      <MenuButton
        v-if="split"
        as="button"
        type="button"
        class="inline-flex items-center px-2 sm:px-3 border border-l-0 rounded-r-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
        :class="[
          sizeClasses[size],
          colorClasses[color][variant],
          disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
        ]"
      >
        <ChevronDownIcon class="h-4 w-4" />
      </MenuButton>
    </div>
    
    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <MenuItems
        class="absolute z-50 mt-2 w-56 sm:w-full min-w-[160px] origin-top-left sm:origin-top-right right-0 sm:left-0 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 focus:outline-none overflow-hidden"
      >
        <div class="py-1">
          <template v-for="(item, index) in items" :key="index">
            <!-- Divider -->
            <div 
              v-if="item.divider" 
              class="border-t border-gray-200 dark:border-gray-700 my-1"
            />
            <!-- Header -->
            <h4 
              v-else-if="item.header" 
              class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
            >
              {{ item.header }}
            </h4>
            <!-- Disabled Item -->
            <div 
              v-else-if="item.disabled" 
              class="px-4 py-2 text-sm text-gray-400 cursor-not-allowed"
            >
              {{ item.label || item }}
            </div>
            <!-- Regular Item -->
            <MenuItem v-else v-slot="{ active }">
              <button
                type="button"
                @click="handleSelect(item)"
                class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm transition-colors"
                :class="[
                  active ? 'bg-gray-100 dark:bg-gray-700' : '',
                  item.danger ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200'
                ]"
              >
                <span v-if="item.icon" v-html="item.icon" class="w-4 h-4 shrink-0" />
                {{ item.label || item }}
              </button>
            </MenuItem>
          </template>
        </div>
        
        <!-- Slot for custom items -->
        <slot name="items" />
      </MenuItems>
    </transition>
  </Menu>
</template>