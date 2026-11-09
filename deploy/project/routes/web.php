<?php

use App\Http\Controllers\NotaController;
use Illuminate\Support\Facades\Route;

// Rota raiz: mostra a demo de persistência no lugar da welcome page padrão do Laravel
// (prova que o banco do cliente1 sobrevive a cada deploy — o container do app é
// recriado, mas db1 + volume não são tocados).
Route::get('/', [NotaController::class, 'index']);
Route::post('/notas', [NotaController::class, 'store']);
