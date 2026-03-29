<?php

namespace App\Console\Commands;

use App\Jobs\SendBookDeadlineNotificationJob;
use App\Models\Book;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('books:check-deadline')]
#[Description('Verifica os prazos de devolução dos livros')]
class CheckBookDeadlineCommand extends Command
{
    public function handle(): void
    {
        $books = Book::query()
            ->whereNotNull('borrowed_at')
            ->whereNull('returned_at')
            ->whereNull('notified_at')
            ->whereBetween('deadline', [now(), now()->addHours(12)])
            ->with('borrowedBy')
            ->get();

        if ($books->isEmpty()) {
            $this->info('Nenhum prazo se encerrando nas próximas 12 horas.');
            return;
        }

        foreach ($books as $book) {
            SendBookDeadlineNotificationJob::dispatch($book->getRelation('borrowedBy'), $book);

            $book->update(['notified_at' => now()]);

            $this->info("E-mail enviado para: {$book->getRelation('borrowedBy')->email} - Livro: {$book->title}");
        }

        $this->info("Total de avisos enviados: {$books->count()}");
    }
}
