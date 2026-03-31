<script setup lang="ts">
import api from '@/Services/api'
import { onMounted } from 'vue'
import { useBookStore } from '@/stores/bookStore'
import { useAuthStore } from '@/Stores/authStore'
import { useRouter } from 'vue-router'

const bookStore = useBookStore()
const authStore = useAuthStore()

onMounted(() => {
  bookStore.fetchBooks()
})

function formatDate(date: string | null): string {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function handleBorrow(id: number) {
  const success = await bookStore.borrowBook(id)
  if (!success) {
    alert(bookStore.error)
  }
}

const router = useRouter()

async function handleLogout() {
  await api.post('/logout')
  authStore.logout()
  router.push('/login')
}

async function handleReturn(id: number) {
  const success = await bookStore.returnBook(id)
  if (!success) {
    alert(bookStore.error)
  }
}
</script>

<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>📚 Zievo — Biblioteca</h1>
      <div class="header-info">
        <span>Olá, {{ authStore.user?.name }}</span>
        <span class="total">{{ bookStore.total }} livros</span>
        <button class="btn-logout" @click="handleLogout">Sair</button>
      </div>
    </header>

    <div v-if="bookStore.loading" class="loading">
      Carregando livros...
    </div>

    <div v-else-if="bookStore.error" class="error-message">
      {{ bookStore.error }}
    </div>

    <div v-else>
      <div class="table-wrapper">
        <table>
          <thead>
          <tr>
            <th>Título</th>
            <th>Autor</th>
            <th>Descrição</th>
            <th>Status</th>
            <th>Emprestado para</th>
            <th>Emprestado em</th>
            <th>Prazo</th>
            <th>Devolvido em</th>
            <th>Ações</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="book in bookStore.books" :key="book.id">
            <td>{{ book.title }}</td>
            <td>{{ book.author }}</td>
            <td>{{ book.description ?? '—' }}</td>
            <td>
                                <span
                                  class="badge"
                                  :class="book.borrowed_at && !book.returned_at ? 'badge-borrowed' : 'badge-available'"
                                >
                                    {{ book.borrowed_at && !book.returned_at ? 'Emprestado' : 'Disponível' }}
                                </span>
            </td>
            <td>{{ book.borrowed_by?.name ?? '—' }}</td>
            <td>{{ formatDate(book.borrowed_at) }}</td>
            <td>
                                <span
                                  v-if="book.deadline"
                                  :class="new Date(book.deadline) < new Date() ? 'overdue' : ''"
                                >
                                    {{ formatDate(book.deadline) }}
                                </span>
              <span v-else>—</span>
            </td>
            <td>{{ formatDate(book.returned_at) }}</td>
            <td class="actions">
              <button
                v-if="!book.borrowed_at || book.returned_at"
                class="btn btn-borrow"
                @click="handleBorrow(book.id)"
              >
                Pegar emprestado
              </button>
              <button
                v-if="book.borrowed_at && !book.returned_at && book.borrowed_by?.id === authStore.user?.id"
                class="btn btn-return"
                @click="handleReturn(book.id)"
              >
                Devolver
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <button
          :disabled="bookStore.currentPage === 1"
          @click="bookStore.fetchBooks(bookStore.currentPage - 1)"
        >
          ← Anterior
        </button>
        <span>Página {{ bookStore.currentPage }} de {{ bookStore.lastPage }}</span>
        <button
          :disabled="bookStore.currentPage === bookStore.lastPage"
          @click="bookStore.fetchBooks(bookStore.currentPage + 1)"
        >
          Próxima →
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  font-size: 1.8rem;
  color: #333;
}

.header-info {
  display: flex;
  gap: 1rem;
  align-items: center;
  color: #555;
}

.total {
  background: #4f46e5;
  color: white;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.85rem;
}

.table-wrapper {
  overflow-x: auto;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

thead {
  background: #4f46e5;
  color: white;
}

th {
  padding: 1rem;
  text-align: left;
  font-weight: 500;
  white-space: nowrap;
}

td {
  padding: 0.85rem 1rem;
  border-bottom: 1px solid #f0f0f0;
  color: #444;
  font-size: 0.9rem;
}

tr:hover td {
  background: #f9f9f9;
}

.badge {
  padding: 0.3rem 0.7rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.badge-available {
  background: #d1fae5;
  color: #065f46;
}

.badge-borrowed {
  background: #fee2e2;
  color: #991b1b;
}

.overdue {
  color: #e53e3e;
  font-weight: 500;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn {
  padding: 0.4rem 0.8rem;
  border: none;
  border-radius: 4px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: opacity 0.2s;
  white-space: nowrap;
}

.btn:hover {
  opacity: 0.85;
}

.btn-logout {
  padding: 0.4rem 0.8rem;
  background: transparent;
  color: #e53e3e;
  border: 1px solid #e53e3e;
  border-radius: 4px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-logout:hover {
  background: #e53e3e;
  color: white;
}

.btn-borrow {
  background: #4f46e5;
  color: white;
}

.btn-return {
  background: #10b981;
  color: white;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.pagination button {
  padding: 0.5rem 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: white;
  cursor: pointer;
  transition: background 0.2s;
}

.pagination button:hover:not(:disabled) {
  background: #4f46e5;
  color: white;
  border-color: #4f46e5;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.loading {
  text-align: center;
  padding: 3rem;
  color: #555;
}

.error-message {
  text-align: center;
  padding: 3rem;
  color: #e53e3e;
}
</style>
