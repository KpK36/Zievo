<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

// Auth públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Auth protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Books públicas
Route::get('books/search', [BookController::class, 'search'])->name('books.search');
Route::apiResource('books', BookController::class)->except(['store', 'update', 'destroy']);

// Books protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Borrow protegida
    Route::post('/borrow/{book}', [BookController::class, 'borrow'])->name('books.borrow');
    Route::post('/return/{book}', [BookController::class, 'return'])->name('books.return');

});
