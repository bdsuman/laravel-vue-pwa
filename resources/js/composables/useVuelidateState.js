import { ref, reactive, computed, toRef } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { helpers } from '@vuelidate/validators'

/**
 * Composable for managing Vuelidate state across components
 * @param {Object} rules - Validation rules
 * @param {Object} state - Reactive state object
 * @returns {Object} Vuelidate state and helpers
 */
export function useVuelidateState(rules, state) {
  const v$ = useVuelidate(rules, state)
  
  // Track touched fields
  const touched = reactive({})
  
  // Mark field as touched
  const touchField = (field) => {
    touched[field] = true
  }
  
  // Check if field has errors
  const hasError = (field) => {
    const parts = field.split('.')
    let current = v$.value
    for (const part of parts) {
      current = current[part]
    }
    return current.$error
  }
  
  // Get first error message for field
  const getError = (field) => {
    const parts = field.split('.')
    let current = v$.value
    for (const part of parts) {
      current = current[part]
    }
    return current.$errors.length ? current.$errors[0].$message : ''
  }
  
  // Check if form is valid
  const isValid = computed(() => !v$.value.$invalid)
  
  // Validate all fields
  const validateAll = async () => {
    return await v$.value.$validate()
  }
  
  // Reset validation state
  const resetValidation = () => {
    v$.value.$reset()
    Object.keys(touched).forEach(key => delete touched[key])
  }
  
  // Custom validator helper
  const withMessage = (validator, message) => {
    return helpers.withMessage(message, validator)
  }
  
  return {
    v$,
    touched,
    touchField,
    hasError,
    getError,
    isValid,
    validateAll,
    resetValidation,
    withMessage
  }
}

/**
 * Create a common validation rule
 * @param {string} name - Rule name
 * @param {Function} validator - Validator function
 * @param {string} message - Error message
 * @returns {Object} Validation rule
 */
export function createValidationRule(name, validator, message) {
  return {
    [name]: helpers.withMessage(message, validator)
  }
}

/**
 * Common validation rules factory
 */
export const ValidationRules = {
  required: (message = 'This field is required') => 
    helpers.withMessage(message, helpers.required),
  
  email: (message = 'Please enter a valid email') => 
    helpers.withMessage(message, helpers.email),
  
  minLength: (length, message) => 
    helpers.withMessage(message || `Minimum ${length} characters required`, helpers.minLength(length)),
  
  maxLength: (length, message) => 
    helpers.withMessage(message || `Maximum ${length} characters allowed`, helpers.maxLength(length)),
  
  minValue: (min, message) => 
    helpers.withMessage(message || `Value must be at least ${min}`, helpers.minValue(min)),
  
  maxValue: (max, message) => 
    helpers.withMessage(message || `Value must not exceed ${max}`, helpers.maxValue(max)),
  
  numeric: (message = 'Only numeric values allowed') => 
    helpers.withMessage(message, helpers.numeric),
  
  alpha: (message = 'Only letters allowed') => 
    helpers.withMessage(message, helpers.alpha),
  
  alphaNum: (message = 'Only letters and numbers allowed') => 
    helpers.withMessage(message, helpers.alphaNum),
  
  url: (message = 'Please enter a valid URL') => 
    helpers.withMessage(message, helpers.url),
  
  sameAs: (otherValue, message = 'Values must match') => 
    helpers.withMessage(message, helpers.sameAs(otherValue)),
  
  requiredUnless: (otherProp, value, message = 'This field is required') => 
    helpers.withMessage(message, helpers.requiredUnless(otherProp)),
}

// Export default
export default {
  useVuelidateState,
  createValidationRule,
  ValidationRules
}