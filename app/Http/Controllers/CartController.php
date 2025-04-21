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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'items' => 'required|array',
            'total' => 'required|numeric',
            'guest_token' => 'nullable|string',
        ]);

        // ✅ Insert into orders table
        DB::table('orders')->insert([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'items' => json_encode($validated['items']),
            'total' => $validated['total'],
            'guest_token' => $validated['guest_token'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ✅ Get the inserted order ID
        $orderId = DB::getPdo()->lastInsertId();

        // ✅ Proceed to Mollie payment
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey("test_bcCAhNsRUbRMgnFJvTfAPWpEdTuKQ2");

        $payment = $mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($validated['total'], 2, '.', ''),
            ],
            "description" => "Order #$orderId",
            "redirectUrl" => route('cart.success'),
            "webhookUrl" => route('webhook.mollie'),
            "metadata" => [
                "order_id" => $orderId,
            ],
        ]);

        return response()->json([
            'checkoutUrl' => $payment->getCheckoutUrl(),
        ]);
    }
    public function success()
    {
        return Inertia::render('Items/Success');
    }
}