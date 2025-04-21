<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Cart;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'items' => 'required|array',
                'total' => 'required|numeric',
            ]);

            $orderId = DB::table('orders')->insertGetId([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'items' => json_encode($validated['items']),
                'total' => $validated['total'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey("test_bcCAhNsRUbRMgnFJvTfAPWpEdTuKQ2");

            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => number_format($request->total, 2, '.', ''),
                ],
                "description" => "Order #" . $orderId,
                "redirectUrl" => route('cart.success', [
                    'order_id' => $orderId,
                ]), 
                "webhookUrl" => route('webhook.mollie'),
                "metadata" => [
                    "order_id" => $orderId,
                ],
            ]);
            return response()->json([
                'checkoutUrl' => $payment->getCheckoutUrl(),
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Payment failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong.',
                'details' => $e->getMessage(),
            ], 500);
        }

    }
    public function success(Request $request)
    {
        dd($request->order_id);
        return Inertia::render('Items/Success');
    }
}