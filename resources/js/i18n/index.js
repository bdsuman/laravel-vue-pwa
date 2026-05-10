import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import bn from './locales/bn.json'
import hi from './locales/hi.json'

const savedLocale = localStorage.getItem('locale') || 'en'

export const i18n = createI18n({
  legacy: false,
  locale: savedLocale,
  fallbackLocale: 'en',
  messages: {
    en,
    bn,
    hi
  }
})

export function setLocale(locale) {
  i18n.global.locale.value = locale
  localStorage.setItem('locale', locale)
  document.documentElement.setAttribute('lang', locale)
  // Update axios headers
  if (window.axios) {
    window.axios.defaults.headers.common['Accept-Language'] = locale
  }
}

export default i18n
