<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $query = Produto::with('categoria')->available();

        // Filtro por categoria
        if ($request->has('categoria')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('slug', $request->categoria);
            });
        }

        // Busca
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Ordenação
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('preco', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('preco', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('nome', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('nome', 'desc');
                break;
            default:
                $query->latest();
        }

        $produtos = $query->paginate(12);
        $categorias = Categoria::all();

        return view('produtos.index', compact('produtos', 'categorias'));
    }

    public function show(Produto $produto)
    {
        if (!$produto->status) {
            abort(404);
        }

        $produtosRelacionados = Produto::where('categoria_id', $produto->categoria_id)
            ->where('id', '!=', $produto->id)
            ->available()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('produtos.show', compact('produto', 'produtosRelacionados'));
    }

    // Métodos de admin (protegidos pelo middleware)
    public function create()
    {
        $categorias = Categoria::all();
        return view('admin.produtos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'imagem' => 'nullable|image|max:2048',
            'status' => 'boolean'
        ]);

        $data = $request->all();
        $data['status'] = $request->boolean('status', true);

        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('produtos', 'public');
            $data['imagem'] = $path;
        }

        Produto::create($data);

        return redirect()->route('admin.produtos')
                       ->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Produto $produto)
    {
        $categorias = Categoria::all();
        return view('admin.produtos.edit', compact('produto', 'categorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'imagem' => 'nullable|image|max:2048',
            'status' => 'boolean'
        ]);

        $data = $request->all();
        $data['status'] = $request->boolean('status', true);

        if ($request->hasFile('imagem')) {
            // Remover imagem antiga se existir
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }

            $path = $request->file('imagem')->store('produtos', 'public');
            $data['imagem'] = $path;
        }

        $produto->update($data);

        return redirect()->route('admin.produtos')
                       ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        if ($produto->imagem) {
            Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();

        return redirect()->route('admin.produtos')
                       ->with('success', 'Produto removido com sucesso!');
    }

    public function toggleStatus(Produto $produto)
    {
        $produto->update(['status' => !$produto->status]);

        return redirect()->route('admin.produtos')
                       ->with('success', 'Status do produto atualizado com sucesso!');
    }
}
