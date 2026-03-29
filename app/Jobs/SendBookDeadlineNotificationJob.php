<?php

namespace App\Jobs;

use App\Mail\BookDeadlineMail;
use App\Models\Book;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendBookDeadlineNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly User $user,
        private readonly Book $book
    ){}

    public function handle(): void
    {
        Mail::to($this->user->getAttribute('email'))->send(new BookDeadlineMail($this->user, $this->book));
    }
}
