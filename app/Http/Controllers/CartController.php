<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->product->preco * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Produto $produto)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1|max:'.$produto->quantidade
        ]);

        $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $produto->id)
                        ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $produto->quantidade) {
                return back()->with('error', 'Quantidade solicitada excede o estoque disponível');
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $produto->id,
                'quantity' => $request->quantity
            ]);
        }

        return redirect()->route('public.cart.index')->with('success', 'Produto adicionado ao carrinho!');
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1|max:'.$cart->product->quantidade
        ]);

        $cart->update(['quantity' => $request->quantity]);

        return redirect()->route('public.cart.index')->with('success', 'Carrinho atualizado!');
    }

    public function remove(Cart $cart)
    {
        $cart->delete();
        return redirect()->route('public.cart.index')->with('success', 'Item removido do carrinho!');
    }

    public function clear()
    {
        Auth::user()->cartItems()->delete();
        return redirect()->route('public.cart.index')->with('success', 'Carrinho limpo!');
    }
}
