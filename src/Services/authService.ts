import api from './api'
import type { RegisterForm, AuthResponse } from '@/Types/auth'

export interface LoginForm {
  email: string
  password: string
}

export const authService = {
  async register(form: RegisterForm): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/register', form)
    return response.data
  },

  async login(form: LoginForm): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/login', form)
    return response.data
  },
}
