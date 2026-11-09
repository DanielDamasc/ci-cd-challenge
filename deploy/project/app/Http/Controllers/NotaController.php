<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotaController extends Controller
{
    public function index(): View
    {
        return view('notas', ['notas' => Nota::latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['texto' => 'required|string']);

        Nota::create(['texto' => $request->input('texto')]);

        return redirect('/');
    }
}
