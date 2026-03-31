import { defineStore } from 'pinia'
import { ref } from 'vue'
import { authService } from '@/Services/authService'
import type { RegisterForm } from '@/Types/auth'
import type { LoginForm } from '@/Services/authService'

interface AuthUser {
  id: number
  name: string
  email: string
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<AuthUser | null>(
    localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')!) : null
  )
  const token = ref<string | null>(localStorage.getItem('token'))
  const errors = ref<Record<string, string[]>>({})
  const loading = ref(false)

  async function register(form: RegisterForm) {
    loading.value = true
    errors.value = {}

    try {
      const response = await authService.register(form)

      token.value = response.access_token
      user.value = response.user

      localStorage.setItem('token', response.access_token)
      localStorage.setItem('user', JSON.stringify(response.user))

      return true
    } catch (error: any) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors
      }
      return false
    } finally {
      loading.value = false
    }
  }

  async function login(form: LoginForm) {
    loading.value = true
    errors.value = {}

    try {
      const response = await authService.login(form)
      console.log('Response do register:', response)
      token.value = response.access_token
      user.value = response.user

      localStorage.setItem('token', response.access_token)
      localStorage.setItem('user', JSON.stringify(response.user))

      return true
    } catch (error: any) {
      if (error.response?.status === 401) {
        errors.value = { email: ['Credenciais inválidas.'] }
      }
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors
      }
      return false
    } finally {
      loading.value = false
    }
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return { user, token, errors, loading, register, login, logout }
})
