<?php

namespace App\Mail;

use App\Models\Book;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookDeadlineMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly Book $book
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Prazo de devolução do livro se encerrando' ,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'book-deadline',
        );
    }
}
