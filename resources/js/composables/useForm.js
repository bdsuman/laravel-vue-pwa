import { ref, reactive, computed } from 'vue'

export function useForm(initial = {}) {
  const form = reactive({ ...initial })
  const errors = ref({})
  const loading = ref(false)

  const hasErrors = computed(() => Object.keys(errors.value).length > 0)

  const setError = (field, message) => {
    errors.value[field] = message
  }

  const setErrors = (errorObj) => {
    errors.value = errorObj
  }

  const clearError = (field) => {
    if (errors.value[field]) {
      delete errors.value[field]
    }
  }

  const clearErrors = () => {
    errors.value = {}
  }

  const reset = (newValues = {}) => {
    Object.keys(form).forEach(key => delete form[key])
    Object.assign(form, { ...initial, ...newValues })
    clearErrors()
  }

  const setLoading = (value) => {
    loading.value = value
  }

  return {
    form,
    errors,
    loading,
    hasErrors,
    setError,
    setErrors,
    clearError,
    clearErrors,
    reset,
    setLoading
  }
}

export function useCountdown(initialSeconds = 60) {
  const seconds = ref(initialSeconds)
  const isActive = ref(false)
  let intervalId = null

  const formatted = computed(() => {
    const mins = Math.floor(seconds.value / 60)
    const secs = seconds.value % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
  })

  const start = (duration = initialSeconds) => {
    stop()
    seconds.value = duration
    isActive.value = true
    intervalId = setInterval(() => {
      if (seconds.value > 0) {
        seconds.value--
      } else {
        stop()
      }
    }, 1000)
  }

  const stop = () => {
    if (intervalId) {
      clearInterval(intervalId)
      intervalId = null
    }
    isActive.value = false
  }

  const reset = (duration = initialSeconds) => {
    stop()
    seconds.value = duration
  }

  return {
    seconds,
    isActive,
    formatted,
    start,
    stop,
    reset
  }
}
