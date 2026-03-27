<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyBookRequest;
use App\Http\Requests\IndexBookRequest;
use App\Http\Requests\SearchBookRequest;
use App\Http\Requests\ShowBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public CONST int PER_PAGE = 10;
    public function index(IndexBookRequest $request): JsonResponse
    // verificar se será necessário form request
    {
        return response()->json([
            'data' => Book::query()->paginate()
        ], 200);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        try {
            $book = DB::transaction(function () use ($request) {
                return Book::query()->firstOrCreate([
                    'title'       => $request->validated('title'),
                    'author'      => $request->validated('author'),
                    'description' => $request->validated('description'),
                    'register_by' => auth()->id(),
                ]);
            });

            return response()->json([
                'data' => $book,
            ], 201);

        } catch (\Exception $exception) {
            return response()->json([
                'error'   => $exception->getMessage(),
                'message' => 'Erro ao registrar o livro',
            ], 500);
        }
    }

    public function show(ShowBookRequest $request): JsonResponse
    {
        $book = Book::query()->findOrFail($request->validated('id'));
        return response()->json([
            'data' => $book
        ], 200);
    }

    /**
     * @throws \Throwable
     */
    public function update(UpdateBookRequest $request): JsonResponse
    {
        $book = Book::query()->findOrFail($request->validated('id'));

        if ((int)$book->getAttribute('register_by') !== auth()->id()) {
            return response()->json([
                'message' => 'Você não tem permissão para editar este livro.',
            ], 403);
        }

        try {
            DB::transaction(function () use ($request, $book) {
                $book->updateOrFail([
                    'title'       => $request->validated('title'),
                    'author'      => $request->validated('author'),
                    'description' => $request->validated('description'),
                ]);
            });

            return response()->json([
                'data' => $book,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar o livro.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(DestroyBookRequest $request): JsonResponse
    {
        $book = Book::query()->findOrFail($request->validated('id'));

        if((int)$book->getAttribute('register_by') !== auth()->id()) {
            return response()->json([
                'message' => 'Você não tem permissão para deletar este livro.',
            ], 403);
        }

        return response()->json([
            'data'    => $book->delete(),
            'message' => 'Livro deletado com sucesso.'
        ], 204);
    }

    public function search(SearchBookRequest $request): JsonResponse
    {
        $title = $request->query('title');
        $book  = Book::query()->where('title', 'LIKE', "%{$title}%");
        return response()->json([
            'data' => $book->paginate(perpage: self::PER_PAGE)
        ], 200);
    }
}
