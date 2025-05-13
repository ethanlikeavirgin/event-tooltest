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
        if(request()->cookie('guest_token')) {
            $guestToken = request()->cookie('guest_token');
        }else {
            $guestToken = '';
        }
        $user_id = Auth::id();
        if($user_id) {
            $cartitems = Cart::with('items')->where('guest_token', $guestToken)->get();
        } else {
            $cartitems = Cart::with('items')->where('guest_token', $guestToken)->get();
        }
        return Inertia::render('Items/Cart', ['guest_token' => $guestToken, 'cartitems' => $cartitems, 'user' => Auth::user(),]);
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|string|email',
                'items' => 'required|array',
                'total' => 'required|numeric',
            ]);

            $userId = 1;
            $guestToken = session('guest_token') ?? request()->cookie('guest_token');

            DB::table('cart')->where('guest_token', $guestToken)->update([
                'user_id' => $userId,
            ]);
            
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId, // 👈 Save user_id if logged in, NULL if guest
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'items' => json_encode($validated['items']),
                'total' => $validated['total'],
                'guest_token' => $guestToken,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey("test_bcCAhNsRUbRMgnFJvTfAPWpEdTuKQ2");

            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => number_format($validated['total'], 2, '.', ''),
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
        $guest_token = session('guest_token');
        $current_id = $request->order_id;
        $orders = DB::table('orders')->where('id', $current_id)->first();
        $my_orders = [];
        if($orders->guest_token) {
            $my_orders = DB::table('orders')->where('id', $current_id)->where('guest_token', $guest_token)->first();
            dd($my_orders);
        }
        /*____UPDATE THE STATS____*/
        if($current_id) {
            DB::table('orders')->where('id', $current_id)->update([
                'status' => 'paid',
            ]);
        }
        return Inertia::render('Items/Success', ['current_id' => $current_id, 'orders' => $my_orders]);
    }
}