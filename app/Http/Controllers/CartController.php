<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Cart;
use Mollie\Laravel\Facades\Mollie;

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
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey("test_bcCAhNsRUbRMgnFJvTfAPWpEdTuKQ2");

        $payment = $mollie->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($request->total, 2, '.', ''),
            ],
            "description" => "Order #12345",
            "redirectUrl" => route('cart.success'),
            "webhookUrl" => route('webhook.mollie'),
            "metadata" => [
                "order_id" => "12345",
            ],
        ]);
        return response()->json([
            'checkoutUrl' => $payment->getCheckoutUrl(),
        ]);
    }
    public function success()
    {
        
    }
}