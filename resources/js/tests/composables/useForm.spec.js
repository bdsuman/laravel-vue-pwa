import {describe, it, expect, beforeEach, vi, afterEach } from 'vitest'
import { useCountdown, useForm } from '../../composables/useForm'

describe('useCountdown Composable', () => {
  beforeEach(() => {
    vi.useFakeTimers()
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('should initialize with given seconds', () => {
    const { seconds, isActive } = useCountdown(60)
    
    expect(seconds.value).toBe(60)
    expect(isActive.value).toBe(false)
  })

  it('should format time correctly', () => {
    const { formatted } = useCountdown(65)
    
    expect(formatted.value).toBe('1:05')
  })

  it('should pad single digit seconds with zero', () => {
    const { formatted } = useCountdown(5)
    
    expect(formatted.value).toBe('0:05')
  })

  it('should start countdown and decrement', () => {
    const { seconds, isActive, start } = useCountdown(3)
    
    start(3)
    
    expect(isActive.value).toBe(true)
    expect(seconds.value).toBe(3)
    
    vi.advanceTimersByTime(1000)
    expect(seconds.value).toBe(2)
    
    vi.advanceTimersByTime(1000)
    expect(seconds.value).toBe(1)
  })

  it('should stop when reaching zero', () => {
    const { seconds, isActive, start, stop } = useCountdown(2)
    
    start(2)
    expect(isActive.value).toBe(true)
    
    stop()
    expect(seconds.value).toBe(2) // Stops immediately without decrementing
    expect(isActive.value).toBe(false)
  })

  it('should reset countdown', () => {
    const { seconds, start, reset, isActive } = useCountdown(5)
    
    start(5)
    vi.advanceTimersByTime(2000)
    
    reset(10)
    
    expect(seconds.value).toBe(10)
    expect(isActive.value).toBe(false)
  })
})

describe('useForm Composable', () => {
  it('should initialize with given values', () => {
    const { form, errors, loading } = useForm({ name: 'Test', email: 'test@example.com' })
    
    expect(form.name).toBe('Test')
    expect(form.email).toBe('test@example.com')
    expect(errors.value).toEqual({})
    expect(loading.value).toBe(false)
  })

  it('should set and clear individual errors', () => {
    const { setError, clearError, errors } = useForm({})
    
    setError('email', 'Invalid email')
    expect(errors.value.email).toBe('Invalid email')
    
    clearError('email')
    expect(errors.value.email).toBeUndefined()
  })

  it('should set multiple errors at once', () => {
    const { setErrors, errors } = useForm({})
    
    setErrors({ name: 'Required', email: 'Invalid' })
    
    expect(errors.value).toEqual({ name: 'Required', email: 'Invalid' })
  })

  it('should clear all errors', () => {
    const { setErrors, clearErrors, errors } = useForm({})
    
    setErrors({ name: 'Required', email: 'Invalid' })
    clearErrors()
    
    expect(errors.value).toEqual({})
  })

  it('should detect if has errors', () => {
    const { hasErrors, setError } = useForm({})
    
    expect(hasErrors.value).toBe(false)
    
    setError('name', 'Required')
    
    expect(hasErrors.value).toBe(true)
  })
})