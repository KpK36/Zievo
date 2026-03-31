export interface Book {
  id: number
  title: string
  author: string
  description: string | null
  register_by: number | null
  borrowed_by: {
    id: number
    name: string
  } | null
  borrowed_at: string | null
  returned_at: string | null
  deadline: string | null
  notified_at: string | null
  created_at: string
  updated_at: string
  deleted_at: string | null
}

export interface BookPagination {
  data: Book[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}
