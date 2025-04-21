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
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.counter' => 'required|integer',
            'items.*.line_total' => 'required|numeric',
            'total' => 'required|numeric',
            'guest_token' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Save the order
            $orderId = DB::table('orders')->insertGetId([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'items' => json_encode($validated['items']),
                'total' => $validated['total'],
                'guest_token' => $validated['guest_token'] ?? null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mollie payment
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey("test_bcCAhNsRUbRMgnFJvTfAPWpEdTuKQ2");

            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => number_format($validated['total'], 2, '.', ''),
                ],
                "description" => "Order #$orderId",
                "redirectUrl" => route('cart.success', ['order_id' => $orderId]),
                "webhookUrl" => route('webhook.mollie'),
                "metadata" => [
                    "order_id" => $orderId,
                ],
            ]);

            DB::commit();

            return response()->json([
                'checkoutUrl' => $payment->getCheckoutUrl(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Mollie/payment error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong while processing your payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function success(Request $request)
    {
        $order = DB::table('orders')->where('id', $request->order_id)->first();

        return Inertia::render('Items/Success', [
            'order' => $order,
        ]);
    }
}