<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookRepository
{
    public function getAllBooks(int $perPage): LengthAwarePaginator
    {
        return Book::query()
            ->with('borrowedBy:id,name')
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Book
    {
        return Book::query()->findOrFail($id);
    }

    public function firstOrCreate(array $data): Book
    {
        return Book::query()->firstOrCreate($data);
    }

    /**
     * @throws \Throwable
     */
    public function update(Book $book, array $data): Book
    {
        $book->updateOrFail($data);
        return $book;
    }

    public function delete(Book $book): bool
    {
        return $book->delete();
    }

    public function search(string $title, int $perPage): LengthAwarePaginator
    {
        return Book::query()
            ->where('title', 'LIKE', "%{$title}%")
            ->paginate($perPage);
    }

}
