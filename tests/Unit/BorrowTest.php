<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['auth']->forgetGuards();
    }

    public function test_user_can_borrow_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/borrow/{$book->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Empréstimo feito com sucesso.']);

        $this->assertDatabaseHas('books', [
            'id'         => $book->id,
            'borrowed_by' => $user->id,
        ]);
    }

    public function test_user_cannot_borrow_already_borrowed_book(): void
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();
        $book      = Book::factory()->create([
            'borrowed_by' => $otherUser->getAttribute('id'),
            'borrowed_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson("/api/borrow/{$book->getAttribute('id')}");

        $response->assertStatus(422)
            ->assertJson(['message' => 'Livro já está emprestado.']);
    }

    public function test_user_cannot_borrow_more_than_three_books(): void
    {
        $user = User::factory()->create();

        Book::factory()->count(3)->create([
            'borrowed_by' => $user->getAttribute('id'),
            'borrowed_at' => now(),
        ]);

        $book = Book::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/borrow/{$book->getAttribute('id')}");

        $response->assertStatus(422)
            ->assertJson(['message' => 'Já possui três livros emprestados.']);
    }

    public function test_user_can_return_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'borrowed_by' => $user->getAttribute('id'),
            'borrowed_at' => now(),
            'deadline'    => now()->addDays(2),
        ]);

        $response = $this->actingAs($user)->postJson("/api/return/{$book->getAttribute('id')}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Devolução feita com sucesso.']);

        $this->assertDatabaseHas('books', [
            'id'          => $book->getAttribute('id'),
            'borrowed_by' => null,
        ]);
    }

    public function test_user_cannot_return_book_borrowed_by_other_user(): void
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();
        $book      = Book::factory()->create([
            'borrowed_by' => $otherUser->getAttribute('id'),
            'borrowed_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson("/api/return/{$book->getAttribute('id')}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_borrow_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->postJson("/api/borrow/{$book->getAttribute('id')}");

        $response->assertStatus(401);
    }
}
