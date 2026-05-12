<script setup>
import { computed } from 'vue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: Number,
    default: 1
  },
  total: {
    type: Number,
    required: true
  },
  perPage: {
    type: Number,
    default: 10
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  align: {
    type: String,
    default: 'center',
    validator: (v) => ['left', 'center', 'right'].includes(v)
  },
  showInfo: {
    type: Boolean,
    default: true
  },
  simple: Boolean
})

const emit = defineEmits(['update:modelValue', 'change'])

const totalPages = computed(() => Math.ceil(props.total / props.perPage))

const currentPage = computed({
  get: () => props.modelValue,
  set: (val) => {
    emit('update:modelValue', val)
    emit('change', val)
  }
})

const startItem = computed(() => Math.min((currentPage.value - 1) * props.perPage + 1, props.total))
const endItem = computed(() => Math.min(currentPage.value * props.perPage, props.total))

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = currentPage.value
  const delta = 2

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    pages.push(1)
    
    if (current > delta + 2) pages.push('...')
    
    const start = Math.max(2, current - delta)
    const end = Math.min(total - 1, current + delta)
    
    for (let i = start; i <= end; i++) pages.push(i)
    
    if (current < total - delta - 1) pages.push('...')
    
    pages.push(total)
  }
  
  return pages
})

const goToPage = (page) => {
  if (page === '...' || page < 1 || page > totalPages.value) return
  currentPage.value = page
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const sizeClasses = {
  sm: 'px-2 py-1 text-xs',
  md: 'px-3 py-1.5 text-sm',
  lg: 'px-4 py-2 text-base'
}

const alignClasses = {
  left: 'justify-start',
  center: 'justify-center',
  right: 'justify-end'
}
</script>

<template>
  <nav class="flex" :class="alignClasses[align]" aria-label="Pagination">
    <!-- Simple Mode -->
    <template v-if="simple">
      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="prevPage"
          :disabled="currentPage === 1"
          class="inline-flex items-center gap-1 px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <ChevronLeftIcon class="h-4 w-4" />
          Previous
        </button>
        
        <span class="text-sm text-gray-600 dark:text-gray-400">
          Page {{ currentPage }} of {{ totalPages }}
        </span>
        
        <button
          type="button"
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="inline-flex items-center gap-1 px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Next
          <ChevronRightIcon class="h-4 w-4" />
        </button>
      </div>
    </template>
    
    <!-- Standard Mode -->
    <template v-else>
      <div class="flex items-center gap-1">
        <!-- Info -->
        <div v-if="showInfo" class="hidden sm:block mr-3 text-sm text-gray-600 dark:text-gray-400">
          Showing {{ startItem }} to {{ endItem }} of {{ total }} results
        </div>
        
        <!-- First Page -->
        <button
          type="button"
          @click="goToPage(1)"
          :disabled="currentPage === 1"
          class="hidden sm:inline-flex items-center justify-center w-10 h-10 rounded-lg text-sm font-medium transition-colors"
          :class="[
            currentPage === 1 
              ? 'text-gray-400 cursor-not-allowed' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            sizeClasses[size]
          ]"
        >
          «
        </button>
        
        <!-- Previous -->
        <button
          type="button"
          @click="prevPage"
          :disabled="currentPage === 1"
          class="inline-flex items-center justify-center rounded-lg transition-colors"
          :class="[
            currentPage === 1 
              ? 'text-gray-400 cursor-not-allowed' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            sizeClasses[size]
          ]"
        >
          <ChevronLeftIcon class="h-5 w-5" />
        </button>
        
        <!-- Page Numbers -->
        <template v-for="(page, index) in visiblePages" :key="index">
          <span 
            v-if="page === '...'" 
            class="inline-flex items-center justify-center w-10 h-10 text-gray-500"
          >
            ...
          </span>
          <button
            v-else
            type="button"
            @click="goToPage(page)"
            class="inline-flex items-center justify-center rounded-lg font-medium transition-colors"
            :class="[
              sizeClasses[size],
              page === currentPage 
                ? 'bg-primary-600 text-white hover:bg-primary-700' 
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
            ]"
          >
            {{ page }}
          </button>
        </template>
        
        <!-- Next -->
        <button
          type="button"
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="inline-flex items-center justify-center rounded-lg transition-colors"
          :class="[
            currentPage === totalPages 
              ? 'text-gray-400 cursor-not-allowed' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            sizeClasses[size]
          ]"
        >
          <ChevronRightIcon class="h-5 w-5" />
        </button>
        
        <!-- Last Page -->
        <button
          type="button"
          @click="goToPage(totalPages)"
          :disabled="currentPage === totalPages"
          class="hidden sm:inline-flex items-center justify-center w-10 h-10 rounded-lg text-sm font-medium transition-colors"
          :class="[
            currentPage === totalPages 
              ? 'text-gray-400 cursor-not-allowed' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            sizeClasses[size]
          ]"
        >
          »
        </button>
      </div>
    </template>
  </nav>
</template>