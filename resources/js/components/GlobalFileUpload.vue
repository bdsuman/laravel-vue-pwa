<script setup>
import { ref, computed } from 'vue'
import { PhotoIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: [File, Array, null],
  label: String,
  disabled: Boolean,
  accept: String,
  multiple: Boolean,
  maxSize: {
    type: Number,
    default: 5 // MB
  },
  maxFiles: {
    type: Number,
    default: 5
  },
  error: String,
  hint: String,
  preview: {
    type: Boolean,
    default: true
  },
  id: String
})

const emit = defineEmits(['update:modelValue', 'change', 'error'])

const inputId = computed(() => props.id || `file-${Math.random().toString(36).substr(2, 9)}`)
const fileInput = ref(null)
const isDragging = ref(false)
const previews = ref([])

const formatSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const isImage = (file) => file.type.startsWith('image/')

const handleFiles = (files) => {
  const fileArray = Array.from(files)
  const maxBytes = props.maxSize * 1024 * 1024
  
  // Check file size
  const oversized = fileArray.filter(f => f.size > maxBytes)
  if (oversized.length > 0) {
    emit('error', `File size must be less than ${props.maxSize}MB`)
    return
  }
  
  // Check file count
  const currentFiles = props.multiple ? (props.modelValue || []).length : 0
  if (currentFiles + fileArray.length > props.maxFiles) {
    emit('error', `Maximum ${props.maxFiles} files allowed`)
    return
  }
  
  const validFiles = fileArray.filter(f => {
    if (props.accept) {
      const accepted = props.accept.split(',').map(t => t.trim())
      return accepted.some(type => {
        if (type.startsWith('.')) {
          return f.name.toLowerCase().endsWith(type.toLowerCase())
        }
        if (type.endsWith('/*')) {
          return f.type.startsWith(type.replace('/*', '/'))
        }
        return f.type === type
      })
    }
    return true
  })
  
  if (validFiles.length !== fileArray.length) {
    emit('error', 'Some files were rejected due to type mismatch')
  }
  
  if (props.multiple) {
    const newFiles = [...(props.modelValue || []), ...validFiles]
    emit('update:modelValue', newFiles)
    emit('change', newFiles)
  } else {
    emit('update:modelValue', validFiles[0] || null)
    emit('change', validFiles[0] || null)
  }
  
  // Generate previews for images
  if (props.preview) {
    validFiles.forEach(file => {
      if (isImage(file)) {
        const reader = new FileReader()
        reader.onload = (e) => {
          previews.value.push({
            name: file.name,
            url: e.target.result
          })
        }
        reader.readAsDataURL(file)
      }
    })
  }
}

const handleDragOver = (e) => {
  e.preventDefault()
  isDragging.value = true
}

const handleDragLeave = () => {
  isDragging.value = false
}

const handleDrop = (e) => {
  e.preventDefault()
  isDragging.value = false
  if (!props.disabled) {
    handleFiles(e.dataTransfer.files)
  }
}

const handleClick = () => {
  if (!props.disabled) {
    fileInput.value?.click()
  }
}

const handleChange = (e) => {
  handleFiles(e.target.files)
  e.target.value = ''
}

const removeFile = (index) => {
  if (props.multiple) {
    const newFiles = [...(props.modelValue || [])]
    newFiles.splice(index, 1)
    previews.value.splice(index, 1)
    emit('update:modelValue', newFiles)
    emit('change', newFiles)
  } else {
    emit('update:modelValue', null)
    emit('change', null)
    previews.value = []
  }
}
</script>

<template>
  <div class="w-full">
    <label 
      v-if="label" 
      :for="inputId" 
      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"
    >
      {{ label }}
    </label>
    
    <div
      @click="handleClick"
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
      class="relative border-2 border-dashed rounded-lg p-4 sm:p-6 text-center transition-colors cursor-pointer"
      :class="[
        error 
          ? 'border-red-500 bg-red-50 dark:bg-red-900/20' 
          : isDragging 
            ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' 
            : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 bg-gray-50 dark:bg-gray-800',
        disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
      ]"
    >
      <input
        :id="inputId"
        ref="fileInput"
        type="file"
        :accept="accept"
        :multiple="multiple"
        :disabled="disabled"
        @change="handleChange"
        class="hidden"
      />
      
      <PhotoIcon class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" />
      
      <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        <span class="font-semibold text-primary-600 dark:text-primary-400">Click to upload</span>
        or drag and drop
      </p>
      <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
        {{ accept || 'All files' }} up to {{ maxSize }}MB
      </p>
      <p v-if="multiple" class="mt-1 text-xs text-gray-500 dark:text-gray-500">
        Max {{ maxFiles }} files
      </p>
    </div>
    
    <!-- File Previews -->
    <div v-if="preview && previews.length > 0" class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
      <div 
        v-for="(preview, index) in previews" 
        :key="index"
        class="relative group rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700"
      >
        <img 
          :src="preview.url" 
          :alt="preview.name"
          class="w-full h-20 sm:h-24 object-cover"
        />
        <button
          type="button"
          @click.stop="removeFile(index)"
          class="absolute top-1 right-1 p-1 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"
        >
          <XMarkIcon class="h-3 w-3" />
        </button>
        <p class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 truncate">
          {{ preview.name }}
        </p>
      </div>
    </div>
    
    <!-- Selected Files List (non-preview) -->
    <div v-if="!preview && modelValue" class="mt-2 space-y-1">
      <div 
        v-if="multiple" 
        v-for="(file, index) in modelValue" 
        :key="index"
        class="flex items-center justify-between text-sm bg-gray-100 dark:bg-gray-700 rounded px-3 py-2"
      >
        <span class="truncate text-gray-700 dark:text-gray-300">{{ file.name }}</span>
        <button
          type="button"
          @click="removeFile(index)"
          class="ml-2 text-gray-500 hover:text-red-500"
        >
          <XMarkIcon class="h-4 w-4" />
        </button>
      </div>
      <div v-else class="flex items-center justify-between text-sm bg-gray-100 dark:bg-gray-700 rounded px-3 py-2">
        <span class="truncate text-gray-700 dark:text-gray-300">{{ modelValue.name }}</span>
        <button
          type="button"
          @click="removeFile(0)"
          class="ml-2 text-gray-500 hover:text-red-500"
        >
          <XMarkIcon class="h-4 w-4" />
        </button>
      </div>
    </div>
    
    <p v-if="error" class="mt-1 text-sm text-red-500">{{ error }}</p>
    <p v-else-if="hint" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ hint }}</p>
  </div>
</template>