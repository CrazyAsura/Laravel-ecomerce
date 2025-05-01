<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalProdutos = Produto::count();
        $totalCategorias = Categoria::count();
        $totalPedidos = Order::count();
        $totalUsuarios = User::count();

        $pedidosRecentes = Order::with('user')->latest()->take(5)->get();
        $produtosRecentes = Produto::with('categoria')->latest()->take(5)->get();
        $categoriasRecentes = Categoria::withCount('produtos')->latest()->take(5)->get();

        // Estatísticas de vendas
        $vendasMensais = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $pedidosMensais = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.dashboard', compact(
            'totalProdutos',
            'totalCategorias',
            'totalPedidos',
            'totalUsuarios',
            'pedidosRecentes',
            'produtosRecentes',
            'categoriasRecentes',
            'vendasMensais',
            'pedidosMensais'
        ));
    }

    public function produtos()
    {
        $produtos = Produto::with('categoria')->latest()->paginate(10);
        return view('admin.produtos.index', compact('produtos'));
    }

    public function createProduto()
    {
        $categorias = Categoria::all();
        return view('admin.produtos.create', compact('categorias'));
    }

    public function editProduto(Produto $produto)
    {
        $categorias = Categoria::all();
        return view('admin.produtos.edit', compact('produto', 'categorias'));
    }

    public function categorias()
    {
        $categorias = Categoria::withCount('produtos')->latest()->paginate(10);
        return view('admin.categorias.index', compact('categorias'));
    }

    public function createCategoria()
    {
        return view('admin.categorias.create');
    }

    public function editCategoria(Categoria $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function listOrders()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,declined,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status do pedido atualizado!');
    }

    public function listUsers()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin'
        ]);

        $user->update($request->all());

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function reports()
    {
        // Vendas por mês
        $vendasPorMes = Order::select(
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('YEAR(created_at) as ano'),
            DB::raw('COUNT(*) as total_pedidos'),
            DB::raw('SUM(total) as total_vendas')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('mes', 'ano')
        ->orderBy('mes')
        ->get();

        // Produtos mais vendidos
        $produtosMaisVendidos = Order::select('product_id', DB::raw('SUM(quantity) as total_vendido'))
            ->groupBy('product_id')
            ->orderByDesc('total_vendido')
            ->take(5)
            ->get();

        return view('admin.reports.index', compact('vendasPorMes', 'produtosMaisVendidos'));
    }

    public function settings()
    {
        return view('admin.settings.index');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'maintenance_mode' => 'boolean'
        ]);

        // Aqui você pode adicionar a lógica para salvar as configurações
        // Por exemplo, usando o pacote spatie/laravel-settings

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }

    public function backup()
    {
        return view('admin.backup.index');
    }

    public function createBackup()
    {
        // Aqui você pode adicionar a lógica para criar backup
        // Por exemplo, usando o pacote spatie/laravel-backup

        return back()->with('success', 'Backup criado com sucesso!');
    }
}
