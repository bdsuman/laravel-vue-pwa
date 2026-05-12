import { ref, readonly } from 'vue'

const toasts = ref([])
let toastId = 0

const toastColors = {
  success: 'bg-green-50 dark:bg-green-900/90 border-green-500 text-green-800 dark:text-green-200',
  error: 'bg-red-50 dark:bg-red-900/90 border-red-500 text-red-800 dark:text-red-200',
  warning: 'bg-yellow-50 dark:bg-yellow-900/90 border-yellow-500 text-yellow-800 dark:text-yellow-200',
  info: 'bg-blue-50 dark:bg-blue-900/90 border-blue-500 text-blue-800 dark:text-blue-200'
}

const toastIcons = {
  success: `<svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`,
  error: `<svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>`,
  warning: `<svg class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`,
  info: `<svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
}

/**
 * Global Toast Composable
 * Usage: const toast = useToast()
 *        toast.success('Operation completed!')
 */
export function useToast() {
  const addToast = ({ message, type = 'info', duration = 5000, title }) => {
    const id = ++toastId
    const toast = {
      id,
      message,
      type,
      title,
      duration,
      colorClass: toastColors[type] || toastColors.info,
      icon: toastIcons[type] || toastIcons.info
    }
    
    toasts.value.push(toast)
    
    if (duration > 0) {
      setTimeout(() => removeToast(id), duration)
    }
    
    return id
  }

  const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  // Convenience methods
  const success = (message, options = {}) => addToast({ message, type: 'success', ...options })
  const error = (message, options = {}) => addToast({ message, type: 'error', ...options })
  const warning = (message, options = {}) => addToast({ message, type: 'warning', ...options })
  const info = (message, options = {}) => addToast({ message, type: 'info', ...options })

  return {
    toasts: readonly(toasts),
    addToast,
    removeToast,
    success,
    error,
    warning,
    info
  }
}

// Export the raw toasts for GlobalToast component
export { toasts, toastColors, toastIcons }
