export interface RegisterForm {
  name: string
  email: string
  password: string
}

export interface AuthResponse {
  user: {
    id: number
    name: string
    email: string
  }
  access_token: string
}
