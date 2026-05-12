<script setup>
import { computed, ref, watch } from 'vue'
import GlobalPagination from './GlobalPagination.vue'
import GlobalInput from './GlobalInput.vue'
import GlobalSelect from './GlobalSelect.vue'
import GlobalButton from './GlobalButton.vue'
import GlobalBadge from './GlobalBadge.vue'
import GlobalModal from './GlobalModal.vue'
import GlobalConfirmDialog from './GlobalConfirmDialog.vue'
import { useToast } from '../composables/useToast.js'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { PencilIcon, TrashIcon, EyeIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  columns: {
    type: Array,
    required: true
    // Format: [{ key: 'name', label: 'Name', sortable: true, searchable: true, visible: true, type: 'text|image|badge|custom' }]
  },
  data: {
    type: Array,
    default: () => []
  },
  // Pagination
  pagination: {
    type: Boolean,
    default: true
  },
  total: {
    type: Number,
    default: 0
  },
  perPage: {
    type: Number,
    default: 10
  },
  currentPage: {
    type: Number,
    default: 1
  },
  // Search & Filter
  searchable: {
    type: Boolean,
    default: true
  },
  searchPlaceholder: {
    type: String,
    default: 'Search...'
  },
  filterOptions: {
    type: Array,
    default: () => []
  },
  // Actions
  actions: {
    type: Object,
    default: () => ({
      view: true,
      edit: true,
      delete: true
    })
  },
  // Row selection
  selectable: Boolean,
  selectedRows: Array,
  // Loading
  loading: Boolean,
  // Empty message
  emptyMessage: {
    type: String,
    default: 'No data available'
  },
  // Row click
  rowClick: Boolean
})

const emit = defineEmits([
  'update:currentPage',
  'update:search',
  'update:filter',
  'page-change',
  'search',
  'filter',
  'sort',
  'view',
  'edit',
  'delete',
  'row-click',
  'bulk-action'
])

const toast = useToast()

// Search
const searchQuery = ref('')
const searchTimeout = ref(null)

const handleSearch = () => {
  if (searchTimeout.value) clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(() => {
    emit('update:search', searchQuery.value)
    emit('search', searchQuery.value)
  }, 300)
}

// Filter
const selectedFilter = ref('')
const handleFilter = () => {
  emit('update:filter', selectedFilter.value)
  emit('filter', selectedFilter.value)
}

// Sorting
const sortColumn = ref('')
const sortDirection = ref('asc')

const handleSort = (column) => {
  if (!column.sortable) return
  
  if (sortColumn.value === column.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column.key
    sortDirection.value = 'asc'
  }
  
  emit('sort', { column: sortColumn.value, direction: sortDirection.value })
}

const getSortIcon = (column) => {
  if (sortColumn.value !== column.key) return '↕'
  return sortDirection.value === 'asc' ? '↑' : '↓'
}

// Row Selection
const isSelected = (row) => {
  return props.selectedRows?.some(s => 
    typeof s === 'object' ? s.id === row.id : s === row.id
  )
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    emit('bulk-action', { action: 'deselect-all' })
  } else {
    emit('bulk-action', { action: 'select-all', rows: props.data })
  }
}

const toggleSelect = (row) => {
  emit('bulk-action', { action: 'toggle', row })
}

const allSelected = computed(() => {
  return props.data.length > 0 && props.data.every(row => isSelected(row))
})

// Actions
const showDeleteConfirm = ref(false)
const deleteItem = ref(null)

const handleView = (row) => emit('view', row)
const handleEdit = (row) => emit('edit', row)
const handleDelete = (row) => {
  deleteItem.value = row
  showDeleteConfirm.value = true
}

const confirmDelete = () => {
  if (deleteItem.value) {
    emit('delete', deleteItem.value)
    showDeleteConfirm.value = false
    deleteItem.value = null
    toast.success('Item deleted successfully')
  }
}

const handleRowClick = (row) => {
  if (props.rowClick) {
    emit('row-click', row)
  }
}

// Page change
const handlePageChange = (page) => {
  emit('update:currentPage', page)
  emit('page-change', page)
}

// Get column value
const getColumnValue = (row, column) => {
  const keys = column.key.split('.')
  let value = row
  for (const key of keys) {
    value = value?.[key]
  }
  return value
}

// Format cell value
const formatValue = (row, column) => {
  const value = getColumnValue(row, column)
  
  if (column.format) return column.format(value, row)
  
  switch (column.type) {
    case 'image':
      return `<img src="${value}" alt="" class="w-10 h-10 rounded object-cover" />`
    case 'badge':
      return `<span class="px-2 py-1 rounded-full text-xs font-medium ${
        column.badgeColors?.[value] || 'bg-gray-100 text-gray-800'
      }">${value}</span>`
    case 'date':
      return value ? new Date(value).toLocaleDateString() : '-'
    case 'datetime':
      return value ? new Date(value).toLocaleString() : '-'
    case 'boolean':
      return value ? '✓' : '✕'
    default:
      return value ?? '-'
  }
}
</script>

<template>
  <div class="w-full">
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
      <!-- Search -->
      <div v-if="searchable" class="relative flex-1 max-w-md">
        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
        <input
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          :placeholder="searchPlaceholder"
          class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
        />
      </div>
      
      <!-- Filters -->
      <div v-if="filterOptions.length > 0" class="flex gap-2">
        <select
          v-model="selectedFilter"
          @change="handleFilter"
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"
        >
          <option value="">All</option>
          <option v-for="opt in filterOptions" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>
      </div>
      
      <!-- Slot for custom toolbar items -->
      <div class="flex items-center gap-2">
        <slot name="toolbar" />
      </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <!-- Checkbox Column -->
            <th v-if="selectable" class="px-4 py-3 w-10">
              <input
                type="checkbox"
                :checked="allSelected"
                @change="toggleSelectAll"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
              />
            </th>
            
            <!-- Data Columns -->
            <th
              v-for="column in columns.filter(c => c.visible !== false)"
              :key="column.key"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider"
              :class="{ 'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700': column.sortable }"
              @click="handleSort(column)"
            >
              <div class="flex items-center gap-2">
                {{ column.label }}
                <span v-if="column.sortable" class="text-gray-400">
                  {{ getSortIcon(column) }}
                </span>
              </div>
            </th>
            
            <!-- Actions Column -->
            <th v-if="actions.view || actions.edit || actions.delete" class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <!-- Loading State -->
          <tr v-if="loading">
            <td :colspan="columns.length + (selectable ? 1 : 0) + 1" class="px-4 py-8 text-center">
              <div class="flex justify-center items-center gap-2">
                <svg class="animate-spin h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                <span class="text-gray-500">Loading...</span>
              </div>
            </td>
          </tr>
          
          <!-- Empty State -->
          <tr v-else-if="data.length === 0">
            <td :colspan="columns.length + (selectable ? 1 : 0) + 1" class="px-4 py-8 text-center">
              <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-2">{{ emptyMessage }}</p>
              </div>
            </td>
          </tr>
          
          <!-- Data Rows -->
          <tr
            v-else
            v-for="(row, index) in data"
            :key="index"
            :class="{ 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800': rowClick }"
            @click="handleRowClick(row)"
          >
            <!-- Checkbox -->
            <td v-if="selectable" class="px-4 py-3" @click.stop>
              <input
                type="checkbox"
                :checked="isSelected(row)"
                @change="toggleSelect(row)"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
              />
            </td>
            
            <!-- Data Cells -->
            <td
              v-for="column in columns.filter(c => c.visible !== false)"
              :key="column.key"
              class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100"
            >
              <slot :name="`cell-${column.key}`" :row="row" :column="column" :value="getColumnValue(row, column)">
                <span v-html="formatValue(row, column)" />
              </slot>
            </td>
            
            <!-- Actions -->
            <td v-if="actions.view || actions.edit || actions.delete" class="px-4 py-3 text-right" @click.stop>
              <div class="flex items-center justify-end gap-1">
                <button
                  v-if="actions.view"
                  @click="handleView(row)"
                  class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30"
                  title="View"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                
                <button
                  v-if="actions.edit"
                  @click="handleEdit(row)"
                  class="p-1.5 rounded-lg text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/30"
                  title="Edit"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                
                <button
                  v-if="actions.delete"
                  @click="handleDelete(row)"
                  class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30"
                  title="Delete"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
                
                <slot name="actions" :row="row" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && !loading && data.length > 0" class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
      <div class="text-sm text-gray-500">
        Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, total) }} of {{ total }} results
      </div>
      
      <GlobalPagination
        :modelValue="currentPage"
        :total="total"
        :perPage="perPage"
        @update:modelValue="handlePageChange"
        size="sm"
      />
    </div>
    
    <!-- Delete Confirmation -->
    <GlobalConfirmDialog
      v-model="showDeleteConfirm"
      title="Confirm Delete"
      message="Are you sure you want to delete this item? This action cannot be undone."
      confirmText="Delete"
      cancelText="Cancel"
      confirmColor="danger"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>