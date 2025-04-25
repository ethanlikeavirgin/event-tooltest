<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class PurchaseController extends Controller
{
    public function index()
    {
        $items = Item::where('max', '>=', 1)->where('item_id', '=', NULL)->get();
        $cart = Cart::with('items')->where('user_id', Auth::id())->get();
        $totalprice = $cart->sum('total');
        $totalprice = number_format($totalprice, 2, '.', '');
        return Inertia::render('Items/Purchase', ['items' => $items, 'cart' => $cart, 'totalprice' => $totalprice]);
    }
    public function welcome()
    {
        $items = Item::where('max', '>=', 1)->where('item_id', '=', NULL)->get();
        if(Auth::id()) {
            $cart = Cart::with('items')->where('user_id', Auth::id())->get();
        } else {
            $guestToken = session('guest_token');
            if (!$guestToken) {
                $guestToken = request()->cookie('guest_token'); // <-- Try recover from cookie
                if (!$guestToken) {
                    $guestToken = Str::uuid()->toString();
                }
                session(['guest_token' => $guestToken]);
            }
            // Always refresh cookie to make sure it's synced
            cookie()->queue(cookie('guest_token', $guestToken, 60 * 24 * 30));
            $cart = Cart::with('items')->where('guest_token', $guestToken)->get();
        }

        $totalprice = $cart->sum('total');
        $totalprice = number_format($totalprice, 2, '.', '');

        return Inertia::render('Welcome', ['items' => $items, 'cart' => $cart, 'totalprice' => $totalprice, 'guesttoken' => $guestToken]);
    }

    public function store(Request $request)
    {
        $counter = (int) $request->counter;
        if(session('guest_token')) {
            $guestToken = request()->cookie('guest_token');
        } else {
            $guestToken = '';
        }
        $item = Item::findOrFail($request->item_id);
        // Check available stock
        if ($counter < 1 || $counter > $item->max) {
            return back()->with('error', 'There are not enough tickets.');
        }

        $existingCartItem = Cart::where('user_id', Auth::id())->where('item_id', $request->item_id)->first();

        if ($existingCartItem) {
            // Update existing cart item
            $newCounter = $existingCartItem->counter + $counter;

            // Check if we have enough stock
            if ($newCounter > $item->max) {
                return back()->with('error', 'Not enough tickets available to add more.');
            }

            $existingCartItem->update([
                'counter' => $newCounter,
                'total' => $newCounter * $request->price,
            ]);
        } else {
            if(Auth()){
                $user_id = Auth::id();
            }else {
                $user_id = '';
            }
            // Add new item to cart
            Cart::create([
                'name' => $request->name,
                'item_id' => $request->item_id,
                'counter' => $counter,
                'user_id' => $user_id,
                'guest_token' => $guestToken,
                'total' => $counter * $request->price,
            ]);
        }

        // Update item stock
        $item->update([
            'max' => $item->max - $counter,
        ]);
        if(!Auth::id()) {
            return redirect()->route('purchase.welcome')->with('success', 'Item added to cart successfully.');
        }else {
            return redirect()->route('purchase.index')->with('success', 'Item added to cart successfully.');
        }
    }

    public function show($id)
    {
        $item = Item::where('id', $id)->first();
        /*$detailitems = Item::where('item_id', $id)->get();*/
        $detailitems = Item::where('id', $id)->orWhere('item_id', $id)->get();
        return Inertia::render('Items/Detail', ['item' => $item, 'detailitems' => $detailitems]);
    }
    public function destroy($id)
    {
        $cartItem = Cart::where('id', $id)->where('user_id', Auth::id())->first();

        if ($cartItem) {
            $cartItem->delete(); // Correct method
        }
        
        if(!Auth::id()) {
            return redirect()->route('purchase.welcome')->with('success', 'Item destroyed successfully.');
        }else {
            return redirect()->route('purchase.index')->with('success', 'Item destroyed successfully.');
        }
    }
}
