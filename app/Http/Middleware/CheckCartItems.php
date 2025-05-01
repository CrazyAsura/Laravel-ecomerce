<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCartItems
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->cartItems->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Carrinho vazio. Adicione itens antes de finalizar a compra.');
        }
        return $next($request);
    }
}
