<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Teste de persistência</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 0 16px; }
        textarea { width: 100%; height: 80px; font-size: 1rem; }
        button { margin-top: 8px; padding: 8px 16px; font-size: 1rem; }
        ul { padding-left: 0; list-style: none; }
        li { background: #f4f4f4; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
        small { color: #666; }
    </style>
</head>
<body>
    <p><strong>Esta aplicação é uma demonstração do seminário "Desafio CI/CD".</strong></p>

    <h1>Teste de persistência</h1>
    <p>Qualquer texto salvo aqui fica gravado no banco do {{ $cliente }}.</p>

    <form method="POST" action="/notas">
        @csrf
        <textarea name="texto" placeholder="Escreva qualquer coisa..." required></textarea>
        <br>
        <button type="submit">Salvar</button>
    </form>

    <h2>Notas salvas</h2>
    @if ($notas->isEmpty())
        <p>Nenhuma nota salva ainda.</p>
    @else
        <ul>
            @foreach ($notas as $nota)
                <li>
                    {{ $nota->texto }}
                    <br>
                    <small>{{ $nota->created_at->format('d/m/Y H:i:s') }}</small>
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>
