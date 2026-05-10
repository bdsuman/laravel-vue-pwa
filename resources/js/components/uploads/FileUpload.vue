<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '../lib/api'

const { t } = useI18n()

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  directory: {
    type: String,
    default: 'uploads'
  },
  accept: {
    type: String,
    default: 'image/*'
  },
  maxSize: {
    type: Number,
    default: 10 * 1024 * 1024 // 10MB
  }
})

const emit = defineEmits(['update:modelValue'])

const file = ref(null)
const preview = ref(null)
const uploading = ref(false)
const error = ref(null)
const dragOver = ref(false)

// Crop state (for image files)
const cropMode = ref(false)
const cropData = ref({
  x: 0,
  y: 0,
  width: 100,
  height: 100
})

const hasFile = computed(() => !!props.modelValue)

function handleFileSelect(event) {
  const selectedFile = event.target.files?.[0] || event.dataTransfer?.files?.[0]
  if (selectedFile) {
    processFile(selectedFile)
  }
}

function processFile(selectedFile) {
  error.value = null
  
  if (selectedFile.size > props.maxSize) {
    error.value = t('upload.file_too_large')
    return
  }

  file.value = selectedFile
  
  if (selectedFile.type.startsWith('image/')) {
    const reader = new FileReader()
    reader.onload = (e) => {
      preview.value = e.target.result
      cropMode.value = true
    }
    reader.readAsDataURL(selectedFile)
  } else {
    preview.value = null
    cropMode.value = false
  }
}

function handleDragOver(e) {
  e.preventDefault()
  dragOver.value = true
}

function handleDragLeave() {
  dragOver.value = false
}

function handleDrop(e) {
  e.preventDefault()
  dragOver.value = false
  const droppedFile = e.dataTransfer?.files?.[0]
  if (droppedFile) {
    processFile(droppedFile)
  }
}

async function uploadFile() {
  if (!file.value) return

  uploading.value = true
  error.value = null

  const formData = new FormData()
  formData.append('file', file.value)
  formData.append('directory', props.directory)

  // Add crop data if in crop mode
  if (cropMode.value && preview.value) {
    formData.append('crop', JSON.stringify(cropData.value))
  }

  try {
    const response = await api.post('/api/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    if (response.data.success) {
      emit('update:modelValue', response.data.data.path)
      file.value = null
      preview.value = null
      cropMode.value = false
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Upload failed'
  } finally {
    uploading.value = false
  }
}

function removeFile() {
  file.value = null
  preview.value = null
  emit('update:modelValue', '')
  cropMode.value = false
  cropData.value = { x: 0, y: 0, width: 100, height: 100 }
}
</script>

<template>
  <div class="file-upload">
    <!-- Current File Display -->
    <div v-if="hasFile && !file" class="mb-4">
      <div class="flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
        <div class="flex items-center gap-3">
          <img v-if="modelValue.endsWith('.jpg') || modelValue.endsWith('.png')" 
               :src="modelValue" 
               class="w-12 h-12 object-cover rounded" />
          <div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">File uploaded</p>
            <p class="text-xs text-gray-500">{{ modelValue }}</p>
          </div>
        </div>
        <button 
          @click="removeFile"
          class="p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900 rounded"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Upload Area -->
    <div 
      v-if="!hasFile || file"
      class="border-2 border-dashed rounded-lg p-6 text-center"
      :class="[
        dragOver ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600',
        file ? 'border-blue-300 dark:border-blue-700' : ''
      ]"
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
    >
      <!-- Preview -->
      <div v-if="preview" class="mb-4">
        <img :src="preview" class="max-h-48 mx-auto rounded-lg shadow" />
        
        <!-- Crop Controls -->
        <div v-if="cropMode" class="mt-4 grid grid-cols-4 gap-3">
          <div>
            <label class="block text-xs text-gray-500">X</label>
            <input v-model.number="cropData.x" type="number" min="0" class="w-full px-2 py-1 border rounded" />
          </div>
          <div>
            <label class="block text-xs text-gray-500">Y</label>
            <input v-model.number="cropData.y" type="number" min="0" class="w-full px-2 py-1 border rounded" />
          </div>
          <div>
            <label class="block text-xs text-gray-500">Width</label>
            <input v-model.number="cropData.width" type="number" min="1" class="w-full px-2 py-1 border rounded" />
          </div>
          <div>
            <label class="block text-xs text-gray-500">Height</label>
            <input v-model.number="cropData.height" type="number" min="1" class="w-full px-2 py-1 border rounded" />
          </div>
        </div>
      </div>

      <!-- Drop Zone -->
      <div v-else class="py-4">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('upload.dragDrop') }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ t('upload.browse') }}</p>
      </div>

      <!-- Error -->
      <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>

      <!-- Actions -->
      <div class="mt-4 flex items-center justify-center gap-3">
        <label class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 dark:bg-blue-900 rounded-lg cursor-pointer hover:bg-blue-200 dark:hover:bg-blue-800">
          {{ t('upload.selectFile') }}
          <input 
            type="file" 
            :accept="accept" 
            @change="handleFileSelect" 
            class="hidden" 
          />
        </label>
        
        <button 
          v-if="file"
          @click="uploadFile"
          :disabled="uploading"
          class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50"
        >
          {{ uploading ? t('common.loading') : t('upload.uploadFile') }}
        </button>
        
        <button 
          v-if="file"
          @click="removeFile"
          class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
        >
          {{ t('common.cancel') }}
        </button>
      </div>
    </div>
  </div>
</template>
