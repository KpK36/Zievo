<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_list_books(): void
    {
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [['id', 'title', 'author']],
                ],
            ]);
    }

    public function test_anyone_can_see_a_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson('/api/books/' . $book->getAttribute('id'));

        $response->assertStatus(200)->assertExactJsonStructure([
            'data' => ['id', 'title', 'author', 'borrowed_at', 'borrowed_by', 'register_by', 'returned_at', 'updated_at', 'created_at', 'notified_at', 'deleted_at', 'description', 'deadline'],
        ]);
    }


    public function test_authenticated_user_can_store_book(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/books', [
            'title'       => 'Neuromancer',
            'author'      => 'William Gibson',
            'description' => 'Ficcao Cientifica'
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'data' => ['id', 'title', 'description', 'author'],
        ]);

        $this->assertDatabaseHas('books', [
            'title'       => 'Neuromancer',
            'register_by' => $user->getAttribute('id'),
        ]);

    }

    public function test_unauthenticated_user_cannot_store_book(): void
    {
        $response = $this->postJson('/api/books', [
            'title'  => 'Neuromancer',
            'author' => 'William Gibson',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_update_own_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['register_by' => $user->getAttribute('id')]);

        $response = $this->actingAs($user)->putJson('/api/books/' . $book->getAttribute('id'), [
            'title'       => 'Novo Titulo',
            'author'      => 'Novo Autor',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('books', [
            'id'    => $book->getAttribute('id'),
            'title' => 'Novo Titulo',
            'author' => 'Novo Autor',
        ]);
    }

    public function test_user_cannot_update_other_users_book(): void
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();
        $book      = Book::factory()->create(['register_by' => $otherUser->getAttribute('id')]);

        $response = $this->actingAs($user)->putJson("/api/books/{$book->getAttribute('id')}", [
            'title'  => 'Novo Título',
            'author' => 'Novo Autor',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['register_by' => $user->getAttribute('id')]);

        $response = $this->actingAs($user)->deleteJson("/api/books/{$book->getAttribute('id')}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Livro deletado com sucesso.']);

        $this->assertSoftDeleted('books', ['id' => $book->getAttribute('id')]);
    }

    public function test_user_cannot_delete_other_users_book(): void
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();
        $book      = Book::factory()->create(['register_by' => $otherUser->getAttribute('id')]);

        $response = $this->actingAs($user)->deleteJson("/api/books/{$book->getAttribute('id')}");

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_borrowed_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'register_by' => $user->getAttribute('id'),
            'borrowed_by' => $user->getAttribute('id'),
            'borrowed_at' => now(),
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/books/{$book->getAttribute('id')}");

        $response->assertStatus(403);
    }

}
