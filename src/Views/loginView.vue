<script setup lang="ts">
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/Stores/authStore'
import type { LoginForm } from '@/Services/authService'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive<LoginForm>({
  email: '',
  password: '',
})

async function handleSubmit() {
  const success = await authStore.login(form)

  if (success) {
    router.push('/dashboard')
  }
}
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <h1>Entrar</h1>

      <form @submit.prevent="handleSubmit">

        <div class="form-group">
          <label for="email">E-mail</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="seu@email.com"
          />
          <span
            v-if="authStore.errors.email"
            class="error"
          >
                        {{ authStore.errors.email[0] }}
                    </span>
        </div>

        <div class="form-group">
          <label for="password">Senha</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="Sua senha"
          />
          <span
            v-if="authStore.errors.password"
            class="error"
          >
                        {{ authStore.errors.password[0] }}
                    </span>
        </div>

        <button
          type="submit"
          :disabled="authStore.loading"
        >
          {{ authStore.loading ? 'Entrando...' : 'Entrar' }}
        </button>

      </form>

      <p>
        Não tem conta?
        <router-link to="/register">Criar conta</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.login-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

h1 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #333;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}

label {
  font-size: 0.9rem;
  margin-bottom: 0.3rem;
  color: #555;
}

input {
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.2s;
}

input:focus {
  border-color: #4f46e5;
}

.error {
  color: #e53e3e;
  font-size: 0.8rem;
  margin-top: 0.2rem;
}

button {
  width: 100%;
  padding: 0.75rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  margin-top: 0.5rem;
  transition: background-color 0.2s;
}

button:hover:not(:disabled) {
  background-color: #4338ca;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

p {
  text-align: center;
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #555;
}

a {
  color: #4f46e5;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}
</style>
