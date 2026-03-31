import { defineStore } from 'pinia'
import { ref } from 'vue'
import { bookService } from '@/Services/bookService'
import type { Book, BookPagination } from '@/Types/book.ts'

export const useBookStore = defineStore('book', () => {
  const books = ref<Book[]>([])
  const currentPage = ref(1)
  const lastPage = ref(1)
  const total = ref(0)
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchBooks(page: number = 1) {
    loading.value = true
    error.value = null

    try {
      const response: BookPagination = await bookService.getAll(page)
      books.value = response.data
      currentPage.value = response.current_page
      lastPage.value = response.last_page
      total.value = response.total
    } catch (e: any) {
      error.value = 'Erro ao carregar livros.'
    } finally {
      loading.value = false
    }
  }

  async function borrowBook(id: number) {
    try {
      await bookService.borrow(id)
      await fetchBooks(currentPage.value)
      return true
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao pegar livro emprestado.'
      return false
    }
  }

  async function returnBook(id: number) {
    try {
      await bookService.return(id)
      await fetchBooks(currentPage.value)
      return true
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao devolver livro.'
      return false
    }
  }

  return { books, currentPage, lastPage, total, loading, error, fetchBooks, borrowBook, returnBook }
})
