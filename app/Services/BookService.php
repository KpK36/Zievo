<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Repositories\BookRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class BookService
{
    public CONST int MAX_BORROW_BOOKS = 3;
    public function __construct(private BookRepository $bookRepository)
    {
    }

    public function index(int $perPage): LengthAwarePaginator
    {
        return $this->bookRepository->getAllBooks($perPage);
    }

    public function store(array $data): Book
    {
        return DB::transaction(function () use ($data) {
            return $this->bookRepository->firstOrCreate([
                ...$data,
                'register_by' => auth()->id(),
            ]);
        });
    }

    public function show(int $id): Book
    {
        return $this->bookRepository->findOrFail($id);
    }

    public function update(int $id, array $data): Book
    {
        $book = $this->bookRepository->findOrFail($id);

        if ((int)$book->getAttribute('register_by') !== auth()->id()) {
            abort(403, 'Você não tem permissão para editar este livro.');
        }

        return DB::transaction(function () use ($book, $data) {
            return $this->bookRepository->update($book, $data);
        });
    }

    public function destroy(int $id): bool
    {
        $book = $this->bookRepository->findOrFail($id);

        if ($book->getAttribute('register_by') !== auth()->id()) {
            abort(403, 'Você não tem permissão para deletar este livro.');
        }

        if($book->getAttribute('borrowed_by')) {
            abort(403, 'O livro não pode ser excluído, porque ainda está emprestado.');
        }

        return $this->bookRepository->delete($book);
    }

    public function search(string $title, int $perPage): LengthAwarePaginator
    {
        return $this->bookRepository->search($title, $perPage);
    }

    public function borrow(int $id): array
    {
        $book = $this->bookRepository->findOrFail($id);

        if (!$this->checkIfHasLessThanThree()) {
            abort(422, 'Já possui três livros emprestados.');
        }

        if (!is_null($book->getAttribute('borrowed_at'))) {
            abort(422, 'Livro já está emprestado.');
        }

        $deadline = now()->addDays(2);

        DB::transaction(function () use ($book, $deadline) {
            $this->bookRepository->update($book, [
                'borrowed_by' => auth()->id(),
                'borrowed_at' => now(),
                'deadline'    => $deadline,
                'notified_at' => null,
            ]);
        });

        return ['book' => $book, 'deadline' => $deadline];
    }

    public function return(int $id): Book
    {
        $book = $this->bookRepository->findOrFail($id);

        if ($book->getAttribute('borrowed_by') !== auth()->id()) {
            abort(403, 'Você não tem permissão para devolver este livro.');
        }

        if (empty($book->getAttribute('borrowed_by'))) {
            abort(422, 'Livro já devolvido.');
        }

        return DB::transaction(function () use ($book) {
            return $this->bookRepository->update($book, [
                'borrowed_by' => null,
                'returned_at' => now(),
                'deadline'    => null,
                'notified_at' => null,
            ]);
        });
    }

    private function checkIfHasLessThanThree(): bool
    {
        /** @var User $user */
        $user = auth()->user();
        return $user->borrowedBooks()->count() < self::MAX_BORROW_BOOKS;
    }
}
