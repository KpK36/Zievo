<?php

namespace Tests\Unit;

use App\Jobs\SendBookDeadlineNotificationJob;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckBookDeadlineCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_job_for_books_near_deadline(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Book::factory()->create([
            'borrowed_by' => $user->getAttribute('id'),
            'borrowed_at' => now()->subDay(),
            'deadline'    => now()->addHours(6),
            'returned_at' => null,
            'notified_at' => null,
        ]);

        $this->artisan('books:check-deadline')
            ->assertSuccessful();

        Queue::assertPushed(SendBookDeadlineNotificationJob::class);
    }

    public function test_command_does_not_dispatch_job_for_already_notified_books(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Book::factory()->create([
            'borrowed_by' => $user->getAttribute('id'),
            'borrowed_at' => now()->subDay(),
            'deadline'    => now()->addHours(6),
            'returned_at' => null,
            'notified_at' => now(),
        ]);

        $this->artisan('books:check-deadline')
            ->assertSuccessful();

        Queue::assertNotPushed(SendBookDeadlineNotificationJob::class);
    }

    public function test_command_does_not_dispatch_job_for_returned_books(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Book::factory()->create([
            'borrowed_by' => $user->getAttribute('id'),
            'borrowed_at' => now()->subDay(),
            'deadline'    => now()->addHours(6),
            'returned_at' => now(),
            'notified_at' => null,
        ]);

        $this->artisan('books:check-deadline')
            ->assertSuccessful();

        Queue::assertNotPushed(SendBookDeadlineNotificationJob::class);
    }

    public function test_command_does_not_dispatch_job_for_books_outside_deadline(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Book::factory()->create([
            'borrowed_by' => $user->getAttribute('id'),
            'borrowed_at' => now(),
            'deadline'    => now()->addHours(24),
            'returned_at' => null,
            'notified_at' => null,
        ]);

        $this->artisan('books:check-deadline')
            ->assertSuccessful();

        Queue::assertNotPushed(SendBookDeadlineNotificationJob::class);
    }

}
