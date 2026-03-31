import api from './api'
import type { BookPagination } from '@/Types/book.ts'

export const bookService = {
  async getAll(page: number = 1): Promise<BookPagination> {
    const response = await api.get<{ data: BookPagination }>(`/books?page=${page}`)
    return response.data.data
  },

  async borrow(id: number): Promise<void> {
    await api.post(`/borrow/${id}`)
  },

  async return(id: number): Promise<void> {
    await api.post(`/return/${id}`)
  },
}
