<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Teste de persistência</title>
    <style>
        :root {
            --hand-font: 'Comic Sans MS', 'Chalkboard SE', 'Marker Felt', cursive, sans-serif;
            --ink: #2d2a26;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: #eceef1;
            max-width: 820px;
            margin: 40px auto;
            padding: 0 16px 60px;
        }

        .page-header {
            margin-bottom: 28px;
        }

        .cliente-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1f2430;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 4px 4px 4px 0;
            transform: rotate(-2deg);
            box-shadow: 2px 3px 6px rgba(0, 0, 0, 0.18);
            margin-bottom: 14px;
        }

        .cliente-badge::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #ff6b6b;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.25);
        }

        .intro {
            font-size: 0.85rem;
            color: #6b6760;
            margin: 0 0 4px;
        }

        h1 {
            margin: 4px 0 6px;
            font-size: 1.9rem;
        }

        .subtitle {
            margin: 0;
            color: #55514b;
        }

        .composer {
            background: #fff3b0;
            border-radius: 2px;
            padding: 18px 20px 14px;
            margin: 28px 0 40px;
            max-width: 340px;
            transform: rotate(-1deg);
            box-shadow: 3px 5px 10px rgba(0, 0, 0, 0.15);
        }

        .composer textarea {
            width: 100%;
            height: 90px;
            font-size: 1.05rem;
            font-family: var(--hand-font);
            color: var(--ink);
            background: transparent;
            border: none;
            resize: vertical;
            outline: none;
        }

        .composer textarea::placeholder {
            color: rgba(45, 42, 38, 0.5);
        }

        .composer button {
            margin-top: 6px;
            padding: 8px 18px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
            background: #1f2430;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .composer button:hover {
            background: #3a4156;
        }

        h2 {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .empty-state {
            border: 2px dashed #c7c3bb;
            border-radius: 6px;
            padding: 28px 16px;
            text-align: center;
            color: #8a857c;
        }

        .notes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 28px 22px;
            padding-top: 6px;
            list-style: none;
            padding-left: 0;
        }

        .note-card {
            position: relative;
            padding: 16px 14px 12px;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-radius: 2px;
            box-shadow: 3px 4px 9px rgba(0, 0, 0, 0.15);
            transition: transform 0.15s ease;
        }

        .note-card::before {
            content: "";
            position: absolute;
            top: -9px;
            left: 50%;
            width: 46px;
            height: 16px;
            background: rgba(255, 255, 255, 0.65);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transform: translateX(-50%) rotate(-3deg);
        }

        .note-card:nth-child(4n+1) { background: #fff3b0; transform: rotate(-2deg); }
        .note-card:nth-child(4n+2) { background: #ffd3e2; transform: rotate(2deg); }
        .note-card:nth-child(4n+3) { background: #d6f5dc; transform: rotate(-1.5deg); }
        .note-card:nth-child(4n+4) { background: #cfe8ff; transform: rotate(1.5deg); }

        .note-card:hover {
            transform: rotate(0deg) scale(1.04);
        }

        .note-text {
            font-family: var(--hand-font);
            font-size: 1.05rem;
            line-height: 1.3;
            word-break: break-word;
            margin: 0 0 10px;
        }

        .note-date {
            font-size: 0.72rem;
            color: #6b6760;
            align-self: flex-end;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <span class="cliente-badge">{{ $cliente }}</span>
        <p class="intro">Esta aplicação é uma demonstração do seminário "Desafio CI/CD".</p>
        <h1>Teste de persistência</h1>
        <p class="subtitle">Qualquer texto salvo aqui fica gravado no banco do {{ $cliente }}.</p>
    </div>

    <form class="composer" method="POST" action="/notas">
        @csrf
        <textarea name="texto" placeholder="Escreva qualquer coisa..." required></textarea>
        <br>
        <button type="submit">Salvar</button>
    </form>

    <h2>Notas salvas</h2>
    @if ($notas->isEmpty())
        <p class="empty-state">Nenhuma nota salva ainda.</p>
    @else
        <ul class="notes-grid">
            @foreach ($notas as $nota)
                <li class="note-card">
                    <p class="note-text">{{ $nota->texto }}</p>
                    <small class="note-date">{{ $nota->created_at->format('d/m/Y H:i:s') }}</small>
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>
