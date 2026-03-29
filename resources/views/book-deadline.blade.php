@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html>
    <body>
        <h1>Olá, {{ $user->name }}!</h1>
        <p>O prazo de devolução do livro <strong>{{ $book->title }}</strong> está se encerrando.</p>
        <p>Você tem menos de 12 horas para devolvê-lo.</p>
        <p>Prazo: <strong>{{ Carbon::parse($book->deadline)->format('d/m/Y H:i') }}</strong></p>
        <p>Por favor, realize a devolução o quanto antes para evitar problemas.</p>
    </body>
</html>
