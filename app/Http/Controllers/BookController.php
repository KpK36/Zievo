<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowBookRequest;
use App\Http\Requests\DestroyBookRequest;
use App\Http\Requests\IndexBookRequest;
use App\Http\Requests\ReturnBookRequest;
use App\Http\Requests\SearchBookRequest;
use App\Http\Requests\ShowBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public CONST int PER_PAGE = 10;

    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->bookService->index(self::PER_PAGE),
        ], 200);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->bookService->store($request->validated()),
        ], 201);
    }

    public function show(ShowBookRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->bookService->show($request->validated('id')),
        ], 200);
    }

    /**
     * @throws \Throwable
     */
    public function update(UpdateBookRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->bookService->update(
                $request->validated('id'),
                $request->safe()->except('id'),
            ),
        ], 200);
    }

    public function destroy(DestroyBookRequest $request): JsonResponse
    {
        return response()->json([
            'data'    => $this->bookService->destroy($request->validated('id')),
            'message' => 'Livro deletado com sucesso.',
        ], 200);
    }

    public function search(SearchBookRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->bookService->search(
                title: $request->query('title'),
                perPage: self::PER_PAGE,
            ),
        ], 200);
    }

    public function borrow(BorrowBookRequest $request): JsonResponse
    {
        $result = $this->bookService->borrow($request->validated('id'));

        return response()->json([
            'data'    => $result['book'],
            'message' => 'Empréstimo feito com sucesso.',
            'warning' => 'O livro deve ser devolvido em: ' . $result['deadline']->format('d/m/Y'),
        ], 200);
    }

    public function return(ReturnBookRequest $request): JsonResponse
    {
        return response()->json([
            'data'    => $this->bookService->return($request->validated('id')),
            'message' => 'Devolução feita com sucesso.',
        ], 200);
    }
}
