<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    // Métodos de admin (protegidos pelo middleware)
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias',
            'descricao' => 'nullable|string',
        ]);

        Categoria::create($request->all());

        return redirect()->route('admin.categorias')
                       ->with('success', 'Categoria criada com sucesso!');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias,nome,'.$categoria->id,
            'descricao' => 'nullable|string',
        ]);

        $categoria->update($request->all());

        return redirect()->route('admin.categorias')
                       ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->produtos()->exists()) {
            return redirect()->route('admin.categorias')
                           ->with('error', 'Não é possível excluir categorias com produtos associados');
        }

        $categoria->delete();

        return redirect()->route('admin.categorias')
                       ->with('success', 'Categoria removida com sucesso!');
    }
}
