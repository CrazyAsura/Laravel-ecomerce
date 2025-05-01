<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MercadoPago;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return view('orders.show', compact('order'));
    }

    public function checkout()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio!');
        }

        // Verificar estoque
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->quantidade) {
                return redirect()->route('cart.index')
                    ->with('error', "O produto {$item->product->nome} não tem estoque suficiente");
            }
        }

        $total = $cartItems->sum(function($item) {
            return $item->product->preco * $item->quantity;
        });

        return view('orders.checkout', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_fullname' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zipcode' => 'required|string|max:20',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cash_on_delivery,pix,boleto,credit_card',
        ]);

        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->product->preco * $item->quantity;
        });

        // Criar pedido
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => Order::generateOrderNumber(),
            'status' => Order::STATUS_PENDING,
            'grand_total' => $total,
            'item_count' => $cartItems->count(),
            'payment_method' => $request->payment_method,
            'shipping_fullname' => $request->shipping_fullname,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_zipcode' => $request->shipping_zipcode,
            'shipping_phone' => $request->shipping_phone,
            'notes' => $request->notes,
        ]);

        // Adicionar itens ao pedido
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->preco,
            ]);

            // Atualizar estoque
            $product = $item->product;
            $product->quantidade -= $item->quantity;
            $product->save();
        }

        // Processar pagamento online
        if (in_array($request->payment_method, ['pix', 'boleto', 'credit_card'])) {
            return $this->processOnlinePayment($order, $request);
        }

        // Pagamento na entrega
        Auth::user()->cartItems()->delete();
        return redirect()->route('orders.show', $order)
                       ->with('success', 'Pedido realizado com sucesso!');
    }

    protected function processOnlinePayment(Order $order, Request $request)
    {
        \MercadoPago\SDK::setAccessToken(config('mercadopago.access_token'));

        $preference = new \MercadoPago\Preference();

        // Itens do pedido
        $items = [];
        foreach ($order->items as $item) {
            $mpItem = new \MercadoPago\Item();
            $mpItem->title = $item->product->nome;
            $mpItem->quantity = $item->quantity;
            $mpItem->unit_price = $item->price;
            $items[] = $mpItem;
        }

        // Configurar preferência
        $preference->items = $items;
        $preference->external_reference = $order->order_number;
        $preference->notification_url = route('mercadopago.webhook');
        $preference->back_urls = [
            'success' => route('orders.show', $order),
            'failure' => route('orders.show', $order),
            'pending' => route('orders.show', $order),
        ];
        $preference->auto_return = 'approved';

        // Configurar métodos de pagamento
        $paymentMethods = [
            'pix' => ['pix'],
            'boleto' => ['bolbradesco'],
            'credit_card' => ['visa', 'master', 'amex', 'elo']
        ];

        $preference->payment_methods = [
            'excluded_payment_methods' => [],
            'excluded_payment_types' => [],
            'installments' => 12,
            'default_payment_method_id' => $paymentMethods[$request->payment_method][0] ?? null,
        ];

        $preference->save();

        $order->update(['mercado_pago_id' => $preference->id]);

        return redirect($preference->init_point);
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);

        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'Só é possível cancelar pedidos pendentes');
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);

        // Devolver itens ao estoque
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->quantidade += $item->quantity;
            $product->save();
        }

        return redirect()->route('orders.show', $order)
                       ->with('success', 'Pedido cancelado com sucesso');
    }

    public function handleWebhook(Request $request)
    {
        \MercadoPago\SDK::setAccessToken(config('mercadopago.access_token'));

        $paymentId = $request->input('data.id');
        $payment = \MercadoPago\Payment::find_by_id($paymentId);

        $order = Order::where('order_number', $payment->external_reference)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $this->updateOrderStatus($order, $payment->status);

        return response()->json(['success' => true]);
    }

    protected function updateOrderStatus(Order $order, $status)
    {
        $statusMap = [
            'approved' => Order::STATUS_COMPLETED,
            'pending' => Order::STATUS_PENDING,
            'in_process' => Order::STATUS_PROCESSING,
            'rejected' => Order::STATUS_DECLINED,
            'refunded' => Order::STATUS_REFUNDED,
            'cancelled' => Order::STATUS_CANCELLED
        ];

        $order->update([
            'status' => $statusMap[$status] ?? Order::STATUS_PENDING,
            'is_paid' => $status === 'approved',
            'payment_status' => $status
        ]);
    }
}
