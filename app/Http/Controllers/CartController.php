<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Cart;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        if(session('guest_token')) {
            $guestToken = session('guest_token');
        }else {
            $guestToken = '';
        }
        $user_id = Auth::id();
        if($user_id) {
            $cartitems = Cart::with('items')->where('auth_id', $user_id)->get();
        } else {
            $cartitems = Cart::with('items')->where('guest_token', $guestToken)->get();
        }
        return Inertia::render('Items/Cart', ['guest_token' => $guestToken, 'cartitems' => $cartitems]);
    }
    public function webhook(Request $request)
    {
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey("your_api_key");

        $payment = $mollie->payments->get($request->id); // Mollie sends payment ID here

        $orderId = $payment->metadata->order_id;

        if ($payment->isPaid()) {
            DB::table('orders')->where('id', $orderId)->update([
                'status' => 'paid',
            ]);
        } else {
            DB::table('orders')->where('id', $orderId)->update([
                'status' => 'failed',
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function store(Request $request)
    {
        $orderId = DB::table('orders')->insertGetId([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'items' => json_encode($request->items),
            'total' => $request->total,
            'guest_token' => $request->guest_token ?? null,
            'status' => 'pending', // <-- Track payment status
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $payment = $mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($request->total, 2, '.', ''),
            ],
            "description" => "Order #$orderId",
            "redirectUrl" => route('cart.success', ['order_id' => $orderId]),
            "webhookUrl" => route('webhook.mollie'),
            "metadata" => [
                "order_id" => $orderId,
            ],
        ]);
        
    }
    public function success(Request $request)
    {
        $order = DB::table('orders')->where('id', $request->order_id)->first();

        return Inertia::render('Items/Success', [
            'order' => $order,
        ]);
    }
}