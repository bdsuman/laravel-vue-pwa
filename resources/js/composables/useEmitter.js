import { ref, computed } from 'vue'

/**
 * Event Emitter Composable
 * Usage: const emitter = useEmitter()
 *        emitter.emit('event-name', payload)
 *        emitter.on('event-name', callback)
 *        emitter.off('event-name', callback)
 */
export function useEmitter() {
  const events = ref({})

  const on = (event, callback) => {
    if (!events.value[event]) {
      events.value[event] = []
    }
    events.value[event].push(callback)

    // Return unsubscribe function
    return () => off(event, callback)
  }

  const off = (event, callback) => {
    if (!events.value[event]) return

    if (callback) {
      events.value[event] = events.value[event].filter(cb => cb !== callback)
    } else {
      events.value[event] = []
    }
  }

  const emit = (event, ...args) => {
    if (!events.value[event]) return

    events.value[event].forEach(callback => {
      try {
        callback(...args)
      } catch (err) {
        console.error(`Error in event handler for "${event}":`, err)
      }
    })
  }

  const once = (event, callback) => {
    const wrappedCallback = (...args) => {
      off(event, wrappedCallback)
      callback(...args)
    }
    on(event, wrappedCallback)
  }

  // Clear all events
  const clear = () => {
    events.value = {}
  }

  // Get list of registered events
  const getEvents = () => Object.keys(events.value)

  return {
    events: computed(() => events.value),
    on,
    off,
    emit,
    once,
    clear,
    getEvents
  }
}

// Global emitter instance for cross-component communication
let globalEmitter = null

export function useGlobalEmitter() {
  if (!globalEmitter) {
    globalEmitter = useEmitter()
  }
  return globalEmitter
}
