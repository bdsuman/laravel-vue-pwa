import { describe, it, expect, beforeEach } from 'vitest'
import { useForm, useCountdown } from '../useForm'

describe('useForm', () => {
  it('should initialize with empty errors', () => {
    const { errors } = useForm({ name: '' })
    expect(errors.value).toEqual({})
  })

  it('should set and clear errors', () => {
    const { setError, clearError, errors } = useForm({ name: '' })
    
    setError('name', 'Name is required')
    expect(errors.value.name).toBe('Name is required')
    
    clearError('name')
    expect(errors.value.name).toBeUndefined()
  })

  it('should set multiple errors at once', () => {
    const { setErrors, errors } = useForm({ name: '', email: '' })
    
    setErrors({ name: 'Name required', email: 'Email required' })
    expect(Object.keys(errors.value)).toHaveLength(2)
  })

  it('should clear all errors', () => {
    const { setErrors, clearErrors, errors } = useForm({ name: '' })
    
    setErrors({ name: 'Error' })
    clearErrors()
    expect(errors.value).toEqual({})
  })

  it('should reset form with new values', () => {
    const { form, reset } = useForm({ name: '', email: '' })
    
    form.name = 'John'
    form.email = 'john@example.com'
    reset()
    
    expect(form.name).toBe('')
    expect(form.email).toBe('')
  })
})

describe('useCountdown', () => {
  it('should initialize with given seconds', () => {
    const { seconds } = useCountdown(120)
    expect(seconds.value).toBe(120)
  })

  it('should format time correctly', () => {
    const { formatted } = useCountdown(65)
    expect(formatted.value).toBe('1:05')
  })

  it('should format 0 seconds correctly', () => {
    const { formatted } = useCountdown(0)
    expect(formatted.value).toBe('0:00')
  })

  it('should start countdown', () => {
    const { start, stop } = useCountdown(2)
    
    start(2)
    expect(stop).toBeTruthy()
  })

  it('should reset countdown', () => {
    const { reset, seconds } = useCountdown(60)
    
    seconds.value = 30
    reset(90)
    expect(seconds.value).toBe(90)
  })
})
